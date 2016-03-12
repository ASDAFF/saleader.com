<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Каталог товаров");
$APPLICATION->SetTitle("Каталог товаров");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog", 
	".default", 
	array(
		"IBLOCK_TYPE" => "#CATALOG_IBLOCK_TYPE#",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"TEMPLATE_THEME" => "site",
		"HIDE_NOT_AVAILABLE" => "N",
		"BASKET_URL" => "/personal/cart/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "#SITE_DIR#catalog/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "Y",
		"ADD_SECTION_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"SET_STATUS_404" => "Y",
		"DETAIL_DISPLAY_NAME" => "Y",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_FILTER" => "Y",
		"FILTER_NAME" => "",
		"FILTER_VIEW_MODE" => "VERTICAL",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_PRICE_CODE" => array(
			0 => "BASE",
			1 => "OPT_1",
			2 => "OPT_2",
		),
		"FILTER_OFFERS_FIELD_CODE" => array(
			0 => "PREVIEW_PICTURE",
			1 => "DETAIL_PICTURE",
			2 => "",
		),
		"FILTER_OFFERS_PROPERTY_CODE" => array(
			0 => "COLOR",
			1 => "MATERIAL",
			2 => "SIZE_CLOTHES",
			3 => "",
		),
		"USE_REVIEW" => "Y",
		"MESSAGES_PER_PAGE" => "10",
		"USE_CAPTCHA" => "Y",
		"REVIEW_AJAX_POST" => "Y",
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
		"FORUM_ID" => "1",
		"URL_TEMPLATES_READ" => "",
		"SHOW_LINK_TO_FORUM" => "N",
		"USE_COMPARE" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
			1 => "OPT_1",
			2 => "OPT_2",
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"USE_PRODUCT_QUANTITY" => "Y",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"QUANTITY_FLOAT" => "N",
		"OFFERS_CART_PROPERTIES" => array(
		),
		"SHOW_TOP_ELEMENTS" => "N",
		"SECTION_COUNT_ELEMENTS" => "Y",
		"SECTION_TOP_DEPTH" => "4",
		"SECTIONS_VIEW_MODE" => "TEXT",
		"SECTIONS_SHOW_PARENT_NAME" => "N",
		"PAGE_ELEMENT_COUNT" => "30",
		"LINE_ELEMENT_COUNT" => "3",
		"ELEMENT_SORT_FIELD" => "SHOWS",
		"ELEMENT_SORT_ORDER" => "desc",
		"ELEMENT_SORT_FIELD2" => "SHOWS",
		"ELEMENT_SORT_ORDER2" => "desc",
		"LIST_PROPERTY_CODE" => array(
			0 => "OFFERS",
			1 => "ATT_BRAND",
			2 => "COLOR",
			3 => "ZOOM2",
			4 => "BATTERY_LIFE",
			5 => "SWITCH",
			6 => "GRAF_PROC",
			7 => "LENGTH_OF_CORD",
			8 => "DISPLAY",
			9 => "LOADING_LAUNDRY",
			10 => "FULL_HD_VIDEO_RECORD",
			11 => "INTERFACE",
			12 => "COMPRESSORS",
			13 => "Number_of_Outlets",
			14 => "MAX_RESOLUTION_VIDEO",
			15 => "MAX_BUS_FREQUENCY",
			16 => "MAX_RESOLUTION",
			17 => "FREEZER",
			18 => "POWER_SUB",
			19 => "POWER",
			20 => "HARD_DRIVE_SPACE",
			21 => "MEMORY",
			22 => "OS",
			23 => "ZOOM",
			24 => "PAPER_FEED",
			25 => "SUPPORTED_STANDARTS",
			26 => "VIDEO_FORMAT",
			27 => "SUPPORT_2SIM",
			28 => "MP3",
			29 => "ETHERNET_PORTS",
			30 => "MATRIX",
			31 => "CAMERA",
			32 => "PHOTOSENSITIVITY",
			33 => "DEFROST",
			34 => "SPEED_WIFI",
			35 => "SPIN_SPEED",
			36 => "PRINT_SPEED",
			37 => "SOCKET",
			38 => "IMAGE_STABILIZER",
			39 => "GSM",
			40 => "SIM",
			41 => "TYPE",
			42 => "MEMORY_CARD",
			43 => "TYPE_BODY",
			44 => "TYPE_MOUSE",
			45 => "TYPE_PRINT",
			46 => "CONNECTION",
			47 => "TYPE_OF_CONTROL",
			48 => "TYPE_DISPLAY",
			49 => "TYPE2",
			50 => "REFRESH_RATE",
			51 => "RANGE",
			52 => "AMOUNT_MEMORY",
			53 => "MEMORY_CAPACITY",
			54 => "VIDEO_BRAND",
			55 => "DIAGONAL",
			56 => "RESOLUTION",
			57 => "TOUCH",
			58 => "CORES",
			59 => "LINE_PROC",
			60 => "PROCESSOR",
			61 => "CLOCK_SPEED",
			62 => "TYPE_PROCESSOR",
			63 => "PROCESSOR_SPEED",
			64 => "HARD_DRIVE",
			65 => "HARD_DRIVE_TYPE",
			66 => "Number_of_memory_slots",
			67 => "MAXIMUM_MEMORY_FREQUENCY",
			68 => "TYPE_MEMORY",
			69 => "BLUETOOTH",
			70 => "FM",
			71 => "GPS",
			72 => "HDMI",
			73 => "SMART_TV",
			74 => "USB",
			75 => "WIFI",
			76 => "FLASH",
			77 => "ROTARY_DISPLAY",
			78 => "SUPPORT_3D",
			79 => "SUPPORT_3G",
			80 => "WITH_COOLER",
			81 => "FINGERPRINT",
			82 => "COLLECTION",
			83 => "TOTAL_OUTPUT_POWER",
			84 => "VID_ZASTECHKI",
			85 => "VID_SUMKI",
			86 => "VIDEO",
			87 => "PROFILE",
			88 => "VYSOTA_RUCHEK",
			89 => "GAS_CONTROL",
			90 => "WARRANTY",
			91 => "GRILL",
			92 => "GENRE",
			93 => "OTSEKOV",
			94 => "CONVECTION",
			95 => "INTAKE_POWER",
			96 => "NAZNAZHENIE",
			97 => "BULK",
			98 => "PODKLADKA",
			99 => "SURFACE_COATING",
			100 => "brand_tyres",
			101 => "SEASON",
			102 => "SEASONOST",
			103 => "DUST_COLLECTION",
			104 => "REF",
			105 => "COUNTRY_BRAND",
			106 => "DRYING",
			107 => "REMOVABLE_TOP_COVER",
			108 => "CONTROL",
			109 => "FINE_FILTER",
			110 => "FORM_FAKTOR",
			111 => "SKU_COLOR",
			112 => "CML2_ARTICLE",
			113 => "DELIVERY",
			114 => "HTML",
			115 => "199",
			116 => "ATT_BRAND2",
			117 => "NEWPRODUCT",
			118 => "SALELEADER",
			119 => "SPECIALOFFER",
			120 => "",
		),
		"INCLUDE_SUBSECTIONS" => "Y",
		"LIST_META_KEYWORDS" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_BROWSER_TITLE" => "NAME",
		"LIST_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "DETAIL_PICTURE",
			3 => "",
		),
		"LIST_OFFERS_PROPERTY_CODE" => array(
			0 => "COLOR",
			1 => "SHIRINA_SHINY",
			2 => "PROFILE",
			3 => "Diameter",
			4 => "MATERIAL",
			5 => "MORE_PHOTO",
			6 => "SIZE_CLOTHES",
			7 => "SIZES_SHOES",
			8 => "SIZES_CLOTHES",
			9 => "ARTNUMBER",
			10 => "",
		),
		"LIST_OFFERS_LIMIT" => "0",
		"DETAIL_PROPERTY_CODE" => array(
			0 => "OFFERS",
			1 => "ATT_BRAND",
			2 => "ZOOM2",
			3 => "BATTERY_LIFE",
			4 => "SWITCH",
			5 => "GRAF_PROC",
			6 => "LENGTH_OF_CORD",
			7 => "DISPLAY",
			8 => "LOADING_LAUNDRY",
			9 => "FULL_HD_VIDEO_RECORD",
			10 => "INTERFACE",
			11 => "COMPRESSORS",
			12 => "Number_of_Outlets",
			13 => "MAX_RESOLUTION_VIDEO",
			14 => "MAX_BUS_FREQUENCY",
			15 => "MAX_RESOLUTION",
			16 => "FREEZER",
			17 => "POWER_SUB",
			18 => "POWER",
			19 => "HARD_DRIVE_SPACE",
			20 => "MEMORY",
			21 => "OS",
			22 => "ZOOM",
			23 => "PAPER_FEED",
			24 => "SUPPORTED_STANDARTS",
			25 => "VIDEO_FORMAT",
			26 => "SUPPORT_2SIM",
			27 => "MP3",
			28 => "ETHERNET_PORTS",
			29 => "MATRIX",
			30 => "CAMERA",
			31 => "PHOTOSENSITIVITY",
			32 => "DEFROST",
			33 => "SPEED_WIFI",
			34 => "SPIN_SPEED",
			35 => "PRINT_SPEED",
			36 => "SOCKET",
			37 => "IMAGE_STABILIZER",
			38 => "GSM",
			39 => "SIM",
			40 => "TYPE",
			41 => "MEMORY_CARD",
			42 => "TYPE_BODY",
			43 => "TYPE_MOUSE",
			44 => "TYPE_PRINT",
			45 => "CONNECTION",
			46 => "TYPE_OF_CONTROL",
			47 => "TYPE_DISPLAY",
			48 => "TYPE2",
			49 => "REFRESH_RATE",
			50 => "RANGE",
			51 => "AMOUNT_MEMORY",
			52 => "MEMORY_CAPACITY",
			53 => "VIDEO_BRAND",
			54 => "DIAGONAL",
			55 => "RESOLUTION",
			56 => "TOUCH",
			57 => "CORES",
			58 => "LINE_PROC",
			59 => "PROCESSOR",
			60 => "CLOCK_SPEED",
			61 => "TYPE_PROCESSOR",
			62 => "PROCESSOR_SPEED",
			63 => "HARD_DRIVE",
			64 => "HARD_DRIVE_TYPE",
			65 => "Number_of_memory_slots",
			66 => "MAXIMUM_MEMORY_FREQUENCY",
			67 => "TYPE_MEMORY",
			68 => "BLUETOOTH",
			69 => "FM",
			70 => "GPS",
			71 => "HDMI",
			72 => "SMART_TV",
			73 => "USB",
			74 => "WIFI",
			75 => "FLASH",
			76 => "ROTARY_DISPLAY",
			77 => "SUPPORT_3D",
			78 => "SUPPORT_3G",
			79 => "WITH_COOLER",
			80 => "FINGERPRINT",
			81 => "COLLECTION",
			82 => "TOTAL_OUTPUT_POWER",
			83 => "VID_ZASTECHKI",
			84 => "VID_SUMKI",
			85 => "VIDEO",
			86 => "PROFILE",
			87 => "VYSOTA_RUCHEK",
			88 => "GAS_CONTROL",
			89 => "WARRANTY",
			90 => "GRILL",
			91 => "GENRE",
			92 => "OTSEKOV",
			93 => "CONVECTION",
			94 => "INTAKE_POWER",
			95 => "NAZNAZHENIE",
			96 => "BULK",
			97 => "PODKLADKA",
			98 => "SURFACE_COATING",
			99 => "brand_tyres",
			100 => "SEASON",
			101 => "DUST_COLLECTION",
			102 => "REF",
			103 => "COUNTRY_BRAND",
			104 => "DRYING",
			105 => "REMOVABLE_TOP_COVER",
			106 => "CONTROL",
			107 => "FINE_FILTER",
			108 => "FORM_FAKTOR",
			109 => "SKU_COLOR",
			110 => "CML2_ARTICLE",
			111 => "DELIVERY",
			112 => "PICKUP",
			113 => "HTML",
			114 => "199",
			115 => "ATT_BRAND2",
			116 => "NEWPRODUCT",
			117 => "MANUFACTURER",
			118 => "MATERIAL",
			119 => "",
		),
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_BROWSER_TITLE" => "NAME",
		"DETAIL_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "",
		),
		"DETAIL_OFFERS_PROPERTY_CODE" => array(
			0 => "COLOR",
			1 => "COLOR_REF",
			2 => "MATERIAL",
			3 => "MORE_PHOTO",
			4 => "SIZE_CLOTHES",
			5 => "ARTNUMBER",
			6 => "SIZES_SHOES",
			7 => "SIZES_CLOTHES",
			8 => "",
		),
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"USE_ALSO_BUY" => "Y",
		"ALSO_BUY_ELEMENT_COUNT" => "4",
		"ALSO_BUY_MIN_BUYES" => "1",
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "desc",
		"PAGER_TEMPLATE" => "round",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"ADD_PICT_PROP" => "MORE_PHOTO",
		"LABEL_PROP" => "-",
		"PRODUCT_DISPLAY_MODE" => "Y",
		"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
		"OFFER_TREE_PROPS" => array(
			0 => "COLOR",
			1 => "SIZE_CLOTHES",
			2 => "MATERIAL",
		),
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"DETAIL_USE_VOTE_RATING" => "Y",
		"DETAIL_VOTE_DISPLAY_AS_RATING" => "rating",
		"DETAIL_USE_COMMENTS" => "Y",
		"DETAIL_BLOG_USE" => "Y",
		"DETAIL_VK_USE" => "N",
		"DETAIL_FB_USE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"USE_STORE" => "N",
		"USE_STORE_PHONE" => "Y",
		"USE_STORE_SCHEDULE" => "Y",
		"USE_MIN_AMOUNT" => "N",
		"STORE_PATH" => "/store/#store_id#",
		"MAIN_TITLE" => "Наличие на складах",
		"MIN_AMOUNT" => "10",
		"DETAIL_BRAND_USE" => "Y",
		"DETAIL_BRAND_PROP_CODE" => array(
			0 => "",
			1 => "BRAND_REF",
			2 => "",
		),
		"ADD_SECTIONS_CHAIN" => "Y",
		"COMMON_SHOW_CLOSE_POPUP" => "N",
		"DETAIL_SHOW_MAX_QUANTITY" => "N",
		"DETAIL_BLOG_URL" => "catalog_comments",
		"DETAIL_BLOG_EMAIL_NOTIFY" => "N",
		"DETAIL_FB_APP_ID" => "",
		"USE_SALE_BESTSELLERS" => "Y",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
		"TOP_ADD_TO_BASKET_ACTION" => "ADD",
		"SECTION_ADD_TO_BASKET_ACTION" => "ADD",
		"DETAIL_ADD_TO_BASKET_ACTION" => array(
			0 => "BUY",
		),
		"DETAIL_SHOW_BASIS_PRICE" => "Y",
		"DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
		"DETAIL_DETAIL_PICTURE_MODE" => "IMG",
		"DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
		"DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
		"STORES" => array(
			0 => "1",
		),
		"USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"FIELDS" => array(
			0 => "TITLE",
			1 => "ADDRESS",
			2 => "DESCRIPTION",
			3 => "PHONE",
			4 => "SCHEDULE",
			5 => "EMAIL",
			6 => "IMAGE_ID",
			7 => "COORDINATES",
			8 => "",
		),
		"SHOW_EMPTY_STORE" => "Y",
		"SHOW_GENERAL_STORE_INFORMATION" => "N",
		"USE_BIG_DATA" => "Y",
		"BIG_DATA_RCM_TYPE" => "any",
		"COMMON_ADD_TO_BASKET_ACTION" => "ADD",
		"COMPONENT_TEMPLATE" => ".default",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SECTION_BACKGROUND_IMAGE" => "-",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DETAIL_BACKGROUND_IMAGE" => "-",
		"SHOW_DEACTIVATED" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"REVIEW_IBLOCK_TYPE" => "#REVIEW_IBLOCK_TYPE#",
		"REVIEW_IBLOCK_ID" => "#REVIEW_IBLOCK_ID#",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
		"SEF_URL_TEMPLATES" => array(
			"sections" => "",
			"section" => "#SECTION_CODE_PATH#/",
			"element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#.html",
			"compare" => "compare/",
			"smart_filter" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/",
		),
		"DISABLE_INIT_JS_IN_COMPONENT" => "Y"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>