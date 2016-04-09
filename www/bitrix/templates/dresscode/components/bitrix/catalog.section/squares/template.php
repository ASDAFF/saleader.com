<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult["ITEMS"])):?>
<?
	if ($arParams["DISPLAY_TOP_PAGER"]){
		?><? echo $arResult["NAV_STRING"]; ?><?
	}
?>

	<div class="items productList">
		<?foreach ($arResult["ITEMS"] as $index => $arElement):?>
			<?
				$this->AddEditAction($arElement["ID"], $arElement["EDIT_LINK"], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arElement["ID"], $arElement["DELETE_LINK"], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$arElement["IMAGE"] = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array("width" => 290, "height" => 340), BX_RESIZE_IMAGE_PROPORTIONAL, false);
				if(empty($arElement["IMAGE"])){
					$arElement["IMAGE"]["src"] = SITE_TEMPLATE_PATH."/images/empty.png";
				}
			?>
			<div class="item product sku" id="<?=$this->GetEditAreaId($arElement["ID"]);?>" data-product-id="<?=!empty($arElement["~ID"]) ? $arElement["~ID"] : $arElement["ID"]?>" data-iblock-id="<?=$arElement["SKU_INFO"]["IBLOCK_ID"]?>" data-prop-id="<?=$arElement["SKU_INFO"]["SKU_PROPERTY_ID"]?>">
		
				<div class="tabloid">
					<a href="#" class="removeFromWishlist" data-id="<?=$arElement["~ID"]?>"></a>
					<?if(!empty($arElement["PROPERTIES"]["OFFERS"]["VALUE"])):?>
						<div class="markerContainer">
							<?foreach ($arElement["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker):?>
							    <div class="marker" style="background-color: <?=strstr($arElement["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arElement["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242"?>"><?=$marker?></div>
							<?endforeach;?>
						</div>
					<?endif;?>
					<?if(isset($arElement["PROPERTIES"]["RATING"]["VALUE"])):?>
					    <div class="rating">
					      <i class="m" style="width:<?=($arElement["PROPERTIES"]["RATING"]["VALUE"] * 100 / 5)?>%"></i>
					      <i class="h"></i>
					    </div>
				    <?endif;?>
					<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="picture"><img src="<?=(!empty($arElement["IMAGE"]["src"]) ? $arElement["IMAGE"]["src"] : SITE_TEMPLATE_PATH.'/images/empty.png')?>" alt="<?=$arElement["NAME"]?>"></a>
					<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="name"><span class="middle"><?=$arElement["NAME"]?></span></a>
					<a class="price"><?=$arElement["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?>
						<?if(!empty($arElement["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"]) && $arElement["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] > 0):?>
							<s class="discount"><?=$arElement["MIN_PRICE"]["PRINT_VALUE"]?></s>
						<?endif;?>
					</a>
					<a href="#" class="addCart<?if($arElement["CAN_BUY"] != true && $arElement["CATALOG_QUANTITY"] <= 0):?> disabled<?endif;?>" data-id="<?=$arElement["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="" class="icon"><?=GetMessage("ADDCART_LABEL")?></a>
					<div class="optional">
						<div class="row">
							<a href="#" class="fastBack label" data-id="<?=$arElement["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/fastBack.png" alt="" class="icon"><?=GetMessage("FASTBACK_LABEL")?></a>
							<a href="#" class="addCompare label" data-id="<?=$arElement["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/compare.png" alt="" class="icon"><?=GetMessage("COMPARE_LABEL")?></a>
						</div>
						<div class="row">
							<a href="#" class="addWishlist label" data-id="<?=$arElement["~ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/wishlist.png" alt="" class="icon"><?=GetMessage("WISHLIST_LABEL")?></a>
							<?if(/*$arElement["CATALOG_QUANTITY"] > 0*/$arElement["CAN_BUY"]):?>
								<a class="inStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="" class="icon"><?=GetMessage("AVAILABLE")?></a>
							<?else:?>
								<?if($arElement["CAN_BUY"] == true):?>
									<a class="onOrder label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/onOrder.png" alt="" class="icon"><?=GetMessage("ON_ORDER")?></a>
								<?else:?>
									<a class="outOfStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/outOfStock.png" alt="" class="icon"><?=GetMessage("NOAVAILABLE")?></a>
								<?endif;?>
							<?endif;?>
						</div>						
					</div>
					<?if(!empty($arElement["SKU_PRODUCT"])):?>
						<?if(!empty($arElement["SKU_PROPERTIES"]) && $level = 1):?>
							<?foreach ($arElement["SKU_PROPERTIES"] as $propName => $arNextProp):?>
								<?if(!empty($arNextProp["VALUES"])):?>
									<div class="skuProperty" data-name="<?=$propName?>" data-level="<?=$level++?>">
										<div class="skuPropertyName"><?=$arNextProp["NAME"]?></div>
										<ul class="skuPropertyList">
											<?foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue):?>
												<li class="skuPropertyValue<?if($arNextPropValue["DISABLED"] == "Y"):?> disabled<?elseif($arNextPropValue["SELECTED"] == "Y"):?> selected<?endif;?>" data-name="<?=$propName?>" data-value="<?=$arNextPropValue["VALUE"]?>">
													<a href="#" class="skuPropertyLink">
														<?if(!empty($arNextPropValue["IMAGE"])):?>
															<img src="<?=$arNextPropValue["IMAGE"]["src"]?>">
														<?else:?>
															<?=$arNextPropValue["VALUE"]?>
														<?endif;?>
													</a>
												</li>
											<?endforeach;?>
										</ul>
									</div>
								<?endif;?>
							<?endforeach;?>
						<?endif;?>
					<?endif;?>
				</div>
			</div>
		<?endforeach;?>
		<div class="clear"></div>
	</div>

	<?
		if ($arParams["DISPLAY_BOTTOM_PAGER"]){
			?><? echo $arResult["NAV_STRING"]; ?><?
		}
	?>

	<?if(empty($_GET["PAGEN_1"])):?>
		<div><?=$arResult["~DESCRIPTION"]?></div>
	<?endif;?>

<?else:?>
	<div id="empty">
		<div class="emptyWrapper">
			<div class="pictureContainer">
				<img src="<?=SITE_TEMPLATE_PATH?>/images/emptyFolder.png" alt="<?=GetMessage("EMPTY_HEADING")?>" class="emptyImg">
			</div>
			<div class="info">
				<h3><?=GetMessage("EMPTY_HEADING")?></h3>
				<p><?=GetMessage("EMPTY_TEXT")?></p>
				<a href="<?=SITE_DIR?>" class="back"><?=GetMessage("MAIN_PAGE")?></a>
			</div>
		</div>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "emptyMenu", Array(
			"ROOT_MENU_TYPE" => "left",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => "",
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "left",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N",
			),
			false
		);?>
	</div>
<?endif;?>