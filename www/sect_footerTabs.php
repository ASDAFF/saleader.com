<div id="footerTabsCaption">
	<div class="wrapper">
		<div class="items">
			 <?$APPLICATION->ShowViewContent("sale_viewed_product_view_content_tab");?>
             <?$APPLICATION->ShowViewContent("catalog_top_view_content_tab");?>
		</div>
	</div>
</div>
<div id="footerTabs">
	<div class="wrapper">
		<div class="items">
			 <?$APPLICATION->IncludeComponent(
	"bitrix:sale.viewed.product",
	".default",
	Array(
		"ACTION_VARIABLE" => "action",
		"BASKET_URL" => "/personal/cart/",
		"COMPONENT_TEMPLATE" => ".default",
		"PRODUCT_ID_VARIABLE" => "id",
		"SET_TITLE" => "N",
		"VIEWED_CANBASKET" => "N",
		"VIEWED_CANBUSKET" => "N",
		"VIEWED_CANBUY" => "N",
		"VIEWED_COUNT" => "8",
		"VIEWED_CURRENCY" => "default",
		"VIEWED_IMAGE" => "Y",
		"VIEWED_IMG_HEIGHT" => "200",
		"VIEWED_IMG_WIDTH" => "240",
		"VIEWED_NAME" => "Y",
		"VIEWED_PRICE" => "Y"
	)
);?> <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.bigdata.products",
	"",
	Array(
		"ACTION_VARIABLE" => "action_cbdp",
		"ADDITIONAL_PICT_PROP_17" => "",
		"ADDITIONAL_PICT_PROP_18" => "MORE_PHOTO",
		"ADDITIONAL_PICT_PROP_9" => "MORE_PHOTO",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"BASKET_URL" => "/personal/basket.php",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CART_PROPERTIES_17" => array("",""),
		"CART_PROPERTIES_18" => array(0=>"",1=>"",),
		"CART_PROPERTIES_9" => array("",""),
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"DEPTH" => "",
		"DETAIL_URL" => "",
		"HIDE_NOT_AVAILABLE" => "N",
		"IBLOCK_ID" => "25",
		"IBLOCK_TYPE" => "catalog",
		"ID" => $arResult["ID"],
		"LABEL_PROP_9" => "-",
		"LINE_ELEMENT_COUNT" => "3",
		"MESS_BTN_BUY" => "",
		"MESS_BTN_DETAIL" => "",
		"MESS_BTN_SUBSCRIBE" => "",
		"OFFER_TREE_PROPS_17" => array(),
		"OFFER_TREE_PROPS_18" => array(),
		"PAGE_ELEMENT_COUNT" => "10",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array("BASE","RITAIL"),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PROPERTY_CODE_17" => array("",""),
		"PROPERTY_CODE_18" => array(0=>"",1=>"",),
		"PROPERTY_CODE_9" => array("",""),
		"RCM_TYPE" => "personal",
		"SECTION_CODE" => "",
		"SECTION_ELEMENT_CODE" => "",
		"SECTION_ELEMENT_ID" => "",
		"SECTION_ID" => "",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_FROM_SECTION" => "N",
		"SHOW_IMAGE" => "Y",
		"SHOW_NAME" => "Y",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_PRODUCTS_9" => "Y",
		"TEMPLATE_THEME" => "blue",
		"USE_PRODUCT_QUANTITY" => "N"
	)
);?>
		</div>
	</div>
</div>
 <br>