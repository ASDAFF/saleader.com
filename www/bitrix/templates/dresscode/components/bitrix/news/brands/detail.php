<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?$ELEMENT_ID=$APPLICATION->IncludeComponent("bitrix:news.detail","brands",Array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"USE_SHARE" => "Y",
		"SHARE_HIDE" => "N",
		"SHARE_TEMPLATE" => "",
		"SHARE_HANDLERS" =>"",
		"SHARE_SHORTEN_URL_LOGIN" => "",
		"SHARE_SHORTEN_URL_KEY" => "",
		"AJAX_MODE" => "Y",
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "1",
		"ELEMENT_ID" => "",
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"CHECK_DATES" => "Y",
		"FIELD_CODE" => "",
		"PROPERTY_CODE" =>"",
		"IBLOCK_URL" => "news.php?ID=#IBLOCK_ID#\"",
		"DETAIL_URL" => "",
		"SET_TITLE" => "Y",
		"SET_CANONICAL_URL" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_STATUS_404" => "Y",
		"SET_LAST_MODIFIED" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_ELEMENT_CHAIN" => "Y",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"USE_PERMISSIONS" => "Y",
		"GROUP_PERMISSIONS" =>"",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_TOP_PAGER" => "Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Страница",
		"PAGER_TEMPLATE" => "",
		"PAGER_SHOW_ALL" => "Y",
		"PAGER_BASE_LINK_ENABLE" => "Y",
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "Y",
		"MESSAGE_404" => "",
		"PAGER_BASE_LINK" => "",
		"PAGER_PARAMS_NAME" => "arrPager",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N"
	)
);?>


<?$BASE_PRICE = CCatalogGroup::GetBaseGroup();?>
<?$arSortFields = array(
	"SHOWS" => array(
		"ORDER"=> "DESC",
		"CODE" => "SHOWS",
		"NAME" => GetMessage("CATALOG_SORT_FIELD_SHOWS")
	),	
	"NAME" => array( // параметр в url
		"ORDER"=> "ASC", //в возрастающем порядке
		"CODE" => "NAME", // Код поля для сортировки
		"NAME" => GetMessage("CATALOG_SORT_FIELD_NAME") // имя для вывода в публичной части, редактировать в (/lang/ru/section.php)
	),
	"PRICE_ASC"=> array(
		"ORDER"=> "ASC",
		"CODE" => "CATALOG_PRICE_".$BASE_PRICE["ID"],
		"NAME" => GetMessage("CATALOG_SORT_FIELD_PRICE_ASC")
	),
	"PRICE_DESC" => array(
		"ORDER"=> "DESC",
		"CODE" => "CATALOG_PRICE_".$BASE_PRICE["ID"],
		"NAME" => GetMessage("CATALOG_SORT_FIELD_PRICE_DESC")
	)
);?>

<?if(!empty($_REQUEST["SORT_FIELD"]) && !empty($arSortFields[$_REQUEST["SORT_FIELD"]])){

	setcookie("CATALOG_SORT_FIELD", $_REQUEST["SORT_FIELD"], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");

	$arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_REQUEST["SORT_FIELD"]]["CODE"];
	$arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_REQUEST["SORT_FIELD"]]["ORDER"];	

	$arSortFields[$_REQUEST["SORT_FIELD"]]["SELECTED"] = "Y";

}elseif(!empty($_COOKIE["CATALOG_SORT_FIELD"]) && !empty($arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]])){ // COOKIE
	
	$arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["CODE"];
	$arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_COOKIE["ORDER"]];
	
	$arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["SELECTED"] = "Y";
}
?>

<?$arSortProductNumber = array(
	30 => array("NAME" => 30), 
	60 => array("NAME" => 60), 
	90 => array("NAME" => 90)
);?>

<?if(!empty($_REQUEST["SORT_TO"]) && $arSortProductNumber[$_REQUEST["SORT_TO"]]){
	setcookie("CATALOG_SORT_TO", $_REQUEST["SORT_TO"], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
	$arSortProductNumber[$_REQUEST["SORT_TO"]]["SELECTED"] = "Y";
	$arParams["PAGE_ELEMENT_COUNT"] = $_REQUEST["SORT_TO"];
}elseif (!empty($_COOKIE["CATALOG_SORT_TO"]) && $arSortProductNumber[$_COOKIE["CATALOG_SORT_TO"]]){
	$arSortProductNumber[$_COOKIE["CATALOG_SORT_TO"]]["SELECTED"] = "Y";
	$arParams["PAGE_ELEMENT_COUNT"] = $_COOKIE["CATALOG_SORT_TO"];
}?>

<?$arTemplates = array(
	"SQUARES" => array(
		"CLASS" => "squares"
	),
	"LINE" => array(
		"CLASS" => "line"
	),
	"TABLE" => array(
		"CLASS" => "table"
	)	
);?>

<?if(!empty($_REQUEST["VIEW"]) && $arTemplates[$_REQUEST["VIEW"]]){
	setcookie("CATALOG_VIEW", $_REQUEST["VIEW"], time() + 60 * 60 * 24 * 30 * 12 * 2);
	$arTemplates[$_REQUEST["VIEW"]]["SELECTED"] = "Y";
	$arParams["CATALOG_TEMPLATE"] = $_REQUEST["VIEW"];
}elseif (!empty($_COOKIE["CATALOG_VIEW"]) && $arTemplates[$_COOKIE["CATALOG_VIEW"]]){
	$arTemplates[$_COOKIE["CATALOG_VIEW"]]["SELECTED"] = "Y";
	$arParams["CATALOG_TEMPLATE"] = $_COOKIE["CATALOG_VIEW"];
}else{
	$arTemplates[key($arTemplates)]["SELECTED"] = "Y";
}
?>

<a href="<?=$arResult["FOLDER"]?>" class="backToList"><?=GetMessage("BACK_TO_LIST_PAGE")?></a>
<?
		global $arrFilter;
		$arrFilter["PROPERTY_ATT_BRAND"] = $ELEMENT_ID;
		$countElements = CIBlockElement::GetList(array(), $arrFilter, array(), false);
?>
<?if($countElements):?>
	<div id="catalog">
	<h1 class="brandsHeading"><?=GetMessage("CATALOG_TITLE")?><?=$ELEMENT_NAME?></h1>
		<noindex>
		<div id="catalogLine">
			<?if(!empty($arSortFields)):?>
				<div class="column">
					<div class="label">
						<?=GetMessage("CATALOG_SORT_LABEL");?>
					</div>
					<select name="sortFields" id="selectSortParams">
						<?foreach ($arSortFields as $arSortFieldCode => $arSortField):?>
							<option value="<?=$APPLICATION->GetCurPageParam("SORT_FIELD=".$arSortFieldCode, array("SORT_FIELD"));?>"<?if($arSortField["SELECTED"] == "Y"):?> selected<?endif;?>><?=$arSortField["NAME"]?></option>
						<?endforeach;?>
					</select>
				</div>
			<?endif;?>
			<?if(!empty($arSortProductNumber)):?>
				<div class="column">
					<div class="label">
						<?=GetMessage("CATALOG_SORT_TO_LABEL");?>
					</div>
					<select name="countElements" id="selectCountElements">
						<?foreach ($arSortProductNumber as $arSortNumberElementId => $arSortNumberElement):?>
							<option value="<?=$APPLICATION->GetCurPageParam("SORT_TO=".$arSortNumberElementId, array("SORT_TO"));?>"<?if($arSortNumberElement["SELECTED"] == "Y"):?> selected<?endif;?>><?=$arSortNumberElement["NAME"]?></option>
						<?endforeach;?>
					</select>
				</div>
			<?endif;?>
			<?if(!empty($arTemplates)):?>
				<div class="column">
					<div class="label">
						<?=GetMessage("CATALOG_VIEW_LABEL");?>
					</div>
					<div class="viewList">
						<?foreach ($arTemplates as $arTemplatesCode => $arNextTemplate):?>
							<div class="element"><a rel="nofollow" <?if($arNextTemplate["SELECTED"] != "Y"):?> href="<?=$APPLICATION->GetCurPageParam("VIEW=".$arTemplatesCode, array("VIEW"));?>"<?endif;?> class="<?=$arNextTemplate["CLASS"]?><?if($arNextTemplate["SELECTED"] == "Y"):?> selected<?endif;?>"></a></div>
						<?endforeach;?>
					</div>
				</div>
			<?endif;?>
		</div>
		</noindex>
		<?
			reset($arTemplates);
		?>

		<?$APPLICATION->IncludeComponent(
			"bitrix:catalog.section",
			 !empty($arParams["CATALOG_TEMPLATE"]) ? strtolower($arParams["CATALOG_TEMPLATE"]) : strtolower(key($arTemplates)),
			array(
				"IBLOCK_TYPE" => $arParams["PRODUCT_IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"],
				"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
				"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
				"INCLUDE_SUBSECTIONS" => "Y",
				"FILTER_NAME" => $arParams["PRODUCT_FILTER_NAME"],
				"PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PRODUCT_PROPERTIES"],
				"PAGER_TEMPLATE" => "round",
				'CONVERT_CURRENCY' => $arParams['PRODUCT_CONVERT_CURRENCY'],
				'CURRENCY_ID' => $arParams['PRODUCT_CURRENCY_ID'],
				"SHOW_ALL_WO_SECTION" => "Y",
				"ADD_SECTIONS_CHAIN" => "N",
				"AJAX_MODE" => "Y"
			),
			$component
		);?>

	</div>
<?else:?>
	<style>
		.backToList{
			float: none;
		}
	</style>
<?endif;?>