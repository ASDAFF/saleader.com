<?$APPLICATION->IncludeComponent(
	"dresscode:search.line", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "25",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"PRICE_CODE" => array(
			0 => "BASE",
            0 => "RITAIL"
		),
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
        "HIDE_MEASURES" => 'Y',
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "OFFERS",
			2 => "ATT_BRAND",
			3 => "COLOR",
			112 => "FORM_FAKTOR",
			113 => "SKU_COLOR",
			114 => "CML2_ARTICLE",
			115 => "DELIVERY",
			116 => "PICKUP",
			117 => "USER_ID",
			118 => "BLOG_POST_ID",
			119 => "BLOG_COMMENTS_CNT",
			120 => "VOTE_COUNT",
			121 => "SHOW_MENU",
			122 => "SIMILAR_PRODUCT",
			123 => "RATING",
			124 => "RELATED_PRODUCT",
			125 => "VOTE_SUM",
			126 => "",
		)
	),
	false
);?>