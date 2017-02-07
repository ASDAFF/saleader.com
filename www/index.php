<?
define("INDEX_PAGE", "Y");?>
<?define("MAIN_PAGE", true);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Интернет-магази IP - видеонаблюдения предложит тысячи товаров по выгодной цене. Доставка по Москве и по всей России.");
$APPLICATION->SetPageProperty("keywords", "ipvi, лидер продаж, интернет магазин, лучший интернет магазин");
$APPLICATION->SetPageProperty("title", "Интернет-магазин IP видеонаблюдения");
$APPLICATION->SetTitle("Интернет-магазин IP видеонаблюдения");?>
	<div id="promoBlock">
		<?$APPLICATION->IncludeComponent("dresscode:slider", ".default", array(
	"IBLOCK_TYPE" => "slider",
		"IBLOCK_ID" => "2",
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "3600000",
		"PICTURE_WIDTH" => "1181",
		"PICTURE_HEIGHT" => "555"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "N"
	)
);?>

		<div id="bannersBlock">
			<ul>
				<li><?$APPLICATION->IncludeFile(SITE_DIR."sect_header_banner.php", Array(), Array("MODE" => "text", "NAME" => GetMessage("SECT_HEADER_BANNER"), "TEMPLATE" => "sect_header_banner.php"));?></li>
				<li><?$APPLICATION->IncludeFile(SITE_DIR."sect_header_banner2.php", Array(), Array("MODE" => "text", "NAME" => GetMessage("SECT_HEADER_BANNER2"), "TEMPLATE" => "sect_header_banner2.php"));?></li>
				<li><?$APPLICATION->IncludeFile(SITE_DIR."sect_header_banner3.php", Array(), Array("MODE" => "text", "NAME" => GetMessage("SECT_HEADER_BANNER3"), "TEMPLATE" => "sect_header_banner3.php"));?></li>
			</ul>
		</div>
	</div>
<?$APPLICATION->IncludeComponent(
    "dresscode:slider",
    "middle",
    array(
        "IBLOCK_TYPE" => "slider",
        "IBLOCK_ID" => "4",
        "CACHE_TYPE" => "Y",
        "CACHE_TIME" => "3600000",
        "PICTURE_WIDTH" => "1476",
        "PICTURE_HEIGHT" => "202",
        "COMPONENT_TEMPLATE" => "middle"
    ),
    false,
    array(
        "ACTIVE_COMPONENT" => "Y"
    )
);?>
<?
$APPLICATION->IncludeComponent(
	"dresscode:pop.section",
	".default",
	array(
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"PROP_NAME" => "UF_POPULAR",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "25",
		"PICTURE_WIDTH" => "120",
		"PICTURE_HEIGHT" => "100",
		"PROP_VALUE" => "Y",
		"ELEMENTS_COUNT" => "10",
		"SORT_PROPERTY_NAME" => "7",
		"SORT_VALUE" => "DESC",
		"SELECT_FIELDS" => array(
			0 => "NAME",
			1 => "SECTION_PAGE_URL",
			2 => "DETAIL_PICTURE",
			3 => "UF_IMAGES",
			4 => "UF_MARKER",
			5 => "",
		),
		"POP_LAST_ELEMENT" => "Y",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
);?>

<?$APPLICATION->IncludeComponent(
	"dresscode:offers.product", 
	".default", 
	array(
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"PROP_NAME" => "OFFERS",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "25",
		"PICTURE_WIDTH" => "220",
		"PICTURE_HEIGHT" => "200",
		"PROP_VALUE" => array(
			0 => "_1751",
			1 => "_1752",
			2 => "_1753",
			3 => "_1754",
			4 => "_1755",
		),
		"ELEMENTS_COUNT" => "6",
		"SORT_PROPERTY_NAME" => "SORT",
		"SORT_VALUE" => "ASC",
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"HIDE_NOT_AVAILABLE" => "N",
		"HIDE_MEASURES" => "N"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
);?>

<?$APPLICATION->IncludeComponent("dresscode:brands.list", ".default", array(
	"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "1",
		"SELECT_FIELDS" => array(
			0 => "",
			1 => "*",
			2 => "",
		),
		"PROP_NAME" => "",
		"PROP_VALUE" => "",
		"ELEMENTS_COUNT" => "10",
		"SORT_PROPERTY_NAME" => "7",
		"SORT_VALUE" => "ASC",
		"PICTURE_WIDTH" => "150",
		"PICTURE_HEIGHT" => "120",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "360000",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "simplyText",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => ""
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>