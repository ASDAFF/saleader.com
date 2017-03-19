<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->AddViewContent("hiddenZoneClassEl",'hiddenZone');
$APPLICATION->AddViewContent("hiddenZoneClass",'hiddenZone');$APPLICATION->AddViewContent("hiddenZoneClassEl",'hiddenZone');
$APPLICATION->AddViewContent("hiddenZoneClass",'hiddenZone');
$APPLICATION->SetTitle("Новинки");?><h1>Популярные товары</h1><?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"personal", 
	array(
		"COMPONENT_TEMPLATE" => "personal",
		"ROOT_MENU_TYPE" => "left2",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?><?$APPLICATION->IncludeComponent(
	"dresscode:simple.offers", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "25",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"PROP_NAME" => "OFFERS",
		"PROP_VALUE" => "542",
		"CONVERT_CURRENCY" => "Y",
		"PROPERTY_CODE" => array(
			0 => "OFFERS",
			108 => "COUNTRY_BRAND",
			109 => "DRYING",
			110 => "REMOVABLE_TOP_COVER",
			111 => "CONTROL",
			112 => "FINE_FILTER",
			113 => "FORM_FAKTOR",
			114 => "SKU_COLOR",
			115 => "USER_ID",
			116 => "BLOG_POST_ID",
			117 => "CML2_ARTICLE",
			118 => "DELIVERY",
			119 => "BLOG_COMMENTS_CNT",
			120 => "VOTE_COUNT",
			121 => "MARKER_PHOTO",
			122 => "NEW",
			123 => "DELIVERY_DESC",
			124 => "SIMILAR_PRODUCT",
			125 => "SALE",
			126 => "RATING",
			127 => "PICKUP",
			128 => "RELATED_PRODUCT",
			129 => "VOTE_SUM",
			130 => "MARKER",
			131 => "POPULAR",
			
		),
		"CURRENCY_ID" => "RUB"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>