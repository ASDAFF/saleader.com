<?define("INDEX_PAGE", "Y");?>
<?define("MAIN_PAGE", true);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>					
	<div id="promoBlock">
		<?$APPLICATION->IncludeComponent(
	"dresscode:slider", 
	".default", 
	array(
		"IBLOCK_TYPE" => "#SLIDER_IBLOCK_TYPE#",
		"IBLOCK_ID" => "#SLIDER_IBLOCK_ID#",
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "3600000",
		"PICTURE_WIDTH" => "1181",
		"PICTURE_HEIGHT" => "555"
	),
	false
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
	"dresscode:offers.product", 
	".default", 
	array(
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "3600000",
		"PROP_NAME" => "OFFERS",
		"IBLOCK_TYPE" => "#CATALOG_IBLOCK_TYPE#",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"PICTURE_WIDTH" => "220",
		"PICTURE_HEIGHT" => "200",
		"PROP_VALUE" => #CATALOG_PROP_VALUES#,
		"ELEMENTS_COUNT" => "10",
		"SORT_PROPERTY_NAME" => "SORT",
		"SORT_VALUE" => "ASC",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
);?>
		
<?$APPLICATION->IncludeComponent(
	"dresscode:pop.section", 
	".default", 
	array(
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "3600000",
		"PROP_NAME" => "UF_POPULAR",
		"IBLOCK_TYPE" => "#CATALOG_IBLOCK_TYPE#",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
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
	"dresscode:slider", 
	"middle", 
	array(
		"IBLOCK_TYPE" => "#MIDDLE_SLIDER_IBLOCK_TYPE#",
		"IBLOCK_ID" => "#MIDDLE_SLIDER_IBLOCK_ID#",
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "3600000",
		"PICTURE_WIDTH" => "1476",
		"PICTURE_HEIGHT" => "202"
	),
	false
);?> 	
<?$APPLICATION->IncludeComponent(
	"dresscode:brands.list", 
	".default", 
	array(
		"IBLOCK_TYPE" => "#BRANDS_IBLOCK_TYPE#",
		"IBLOCK_ID" => "#BRANDS_IBLOCK_ID#",
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