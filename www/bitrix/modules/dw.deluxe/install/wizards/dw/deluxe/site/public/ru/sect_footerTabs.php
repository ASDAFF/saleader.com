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
			<?$APPLICATION->IncludeComponent("bitrix:sale.viewed.product", ".default", Array(
				"VIEWED_COUNT" => "12",	// РљРѕР»РёС‡РµСЃС‚РІРѕ СЌР»РµРјРµРЅС‚РѕРІ
					"VIEWED_NAME" => "Y",	// РџРѕРєР°Р·С‹РІР°С‚СЊ РЅР°РёРјРµРЅРѕРІР°РЅРёРµ
					"VIEWED_IMAGE" => "Y",	// РџРѕРєР°Р·С‹РІР°С‚СЊ РёР·РѕР±СЂР°Р¶РµРЅРёРµ
					"VIEWED_PRICE" => "Y",	// РџРѕРєР°Р·С‹РІР°С‚СЊ С†РµРЅСѓ
					"VIEWED_CANBUY" => "N",	// РџРѕРєР°Р·Р°С‚СЊ "РљСѓРїРёС‚СЊ"
					"VIEWED_CANBUSKET" => "N",
					"VIEWED_IMG_HEIGHT" => "340",	// Р’С‹СЃРѕС‚Р° РёР·РѕР±СЂР°Р¶РµРЅРёСЏ
					"VIEWED_IMG_WIDTH" => "290",	// РЁРёСЂРёРЅР° РёР·РѕР±СЂР°Р¶РµРЅРёСЏ
					"BASKET_URL" => "/personal/cart/",	// URL, РІРµРґСѓС‰РёР№ РЅР° СЃС‚СЂР°РЅРёС†Сѓ СЃ РєРѕСЂР·РёРЅРѕР№ РїРѕРєСѓРїР°С‚РµР»СЏ
					"ACTION_VARIABLE" => "action",	// РќР°Р·РІР°РЅРёРµ РїРµСЂРµРјРµРЅРЅРѕР№, РІ РєРѕС‚РѕСЂРѕР№ РїРµСЂРµРґР°РµС‚СЃСЏ РґРµР№СЃС‚РІРёРµ
					"PRODUCT_ID_VARIABLE" => "id",	// РќР°Р·РІР°РЅРёРµ РїРµСЂРµРјРµРЅРЅРѕР№, РІ РєРѕС‚РѕСЂРѕР№ РїРµСЂРµРґР°РµС‚СЃСЏ РєРѕРґ С‚РѕРІР°СЂР° РґР»СЏ РїРѕРєСѓРїРєРё
					"VIEWED_CURRENCY" => "RUB",	// Р’Р°Р»СЋС‚Р°
					"VIEWED_CANBASKET" => "N",	// РџРѕРєР°Р·Р°С‚СЊ "Р’ РєРѕСЂР·РёРЅСѓ"
					"SET_TITLE" => "N",	// РЈСЃС‚Р°РЅР°РІР»РёРІР°С‚СЊ Р·Р°РіРѕР»РѕРІРѕРє СЃС‚СЂР°РЅРёС†С‹
				),
				false
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:catalog.top", ".default", Array(
				"COMPONENT_TEMPLATE" => ".default",
					"IBLOCK_TYPE" => "#CATALOG_IBLOCK_TYPE#",	// РўРёРї РёРЅС„РѕР±Р»РѕРєР°
					"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",	// Р?РЅС„РѕР±Р»РѕРє
					"ELEMENT_SORT_FIELD" => "sort",	// РџРѕ РєР°РєРѕРјСѓ РїРѕР»СЋ СЃРѕСЂС‚РёСЂСѓРµРј СЌР»РµРјРµРЅС‚С‹
					"ELEMENT_SORT_ORDER" => "asc",	// РџРѕСЂСЏРґРѕРє СЃРѕСЂС‚РёСЂРѕРІРєРё СЌР»РµРјРµРЅС‚РѕРІ
					"ELEMENT_SORT_FIELD2" => "id",	// РџРѕР»Рµ РґР»СЏ РІС‚РѕСЂРѕР№ СЃРѕСЂС‚РёСЂРѕРІРєРё СЌР»РµРјРµРЅС‚РѕРІ
					"ELEMENT_SORT_ORDER2" => "desc",	// РџРѕСЂСЏРґРѕРє РІС‚РѕСЂРѕР№ СЃРѕСЂС‚РёСЂРѕРІРєРё СЌР»РµРјРµРЅС‚РѕРІ
					"FILTER_NAME" => "",	// Р?РјСЏ РјР°СЃСЃРёРІР° СЃРѕ Р·РЅР°С‡РµРЅРёСЏРјРё С„РёР»СЊС‚СЂР° РґР»СЏ С„РёР»СЊС‚СЂР°С†РёРё СЌР»РµРјРµРЅС‚РѕРІ
					"HIDE_NOT_AVAILABLE" => "N",	// РќРµ РѕС‚РѕР±СЂР°Р¶Р°С‚СЊ С‚РѕРІР°СЂС‹, РєРѕС‚РѕСЂС‹С… РЅРµС‚ РЅР° СЃРєР»Р°РґР°С…
					"ELEMENT_COUNT" => "12",	// РљРѕР»РёС‡РµСЃС‚РІРѕ РІС‹РІРѕРґРёРјС‹С… СЌР»РµРјРµРЅС‚РѕРІ
					"LINE_ELEMENT_COUNT" => "1",	// РљРѕР»РёС‡РµСЃС‚РІРѕ СЌР»РµРјРµРЅС‚РѕРІ РІС‹РІРѕРґРёРјС‹С… РІ РѕРґРЅРѕР№ СЃС‚СЂРѕРєРµ С‚Р°Р±Р»РёС†С‹
					"PROPERTY_CODE" => array(	// РЎРІРѕР№СЃС‚РІР°
						0 => "",
						1 => "",
					),
					"OFFERS_FIELD_CODE" => array(	// РџРѕР»СЏ РїСЂРµРґР»РѕР¶РµРЅРёР№
						0 => "",
						1 => "",
					),
					"OFFERS_PROPERTY_CODE" => array(	// РЎРІРѕР№СЃС‚РІР° РїСЂРµРґР»РѕР¶РµРЅРёР№
						0 => "",
						1 => "",
					),
					"OFFERS_SORT_FIELD" => "sort",	// РџРѕ РєР°РєРѕРјСѓ РїРѕР»СЋ СЃРѕСЂС‚РёСЂСѓРµРј РїСЂРµРґР»РѕР¶РµРЅРёСЏ С‚РѕРІР°СЂР°
					"OFFERS_SORT_ORDER" => "asc",	// РџРѕСЂСЏРґРѕРє СЃРѕСЂС‚РёСЂРѕРІРєРё РїСЂРµРґР»РѕР¶РµРЅРёР№ С‚РѕРІР°СЂР°
					"OFFERS_SORT_FIELD2" => "id",	// РџРѕР»Рµ РґР»СЏ РІС‚РѕСЂРѕР№ СЃРѕСЂС‚РёСЂРѕРІРєРё РїСЂРµРґР»РѕР¶РµРЅРёР№ С‚РѕРІР°СЂР°
					"OFFERS_SORT_ORDER2" => "desc",	// РџРѕСЂСЏРґРѕРє РІС‚РѕСЂРѕР№ СЃРѕСЂС‚РёСЂРѕРІРєРё РїСЂРµРґР»РѕР¶РµРЅРёР№ С‚РѕРІР°СЂР°
					"OFFERS_LIMIT" => "5",	// РњР°РєСЃРёРјР°Р»СЊРЅРѕРµ РєРѕР»РёС‡РµСЃС‚РІРѕ РїСЂРµРґР»РѕР¶РµРЅРёР№ РґР»СЏ РїРѕРєР°Р·Р° (0 - РІСЃРµ)
					"VIEW_MODE" => "SECTION",	// РџРѕРєР°Р· СЌР»РµРјРµРЅС‚РѕРІ
					"TEMPLATE_THEME" => "blue",	// Р¦РІРµС‚РѕРІР°СЏ С‚РµРјР°
					"PRODUCT_DISPLAY_MODE" => "N",	// РЎС…РµРјР° РѕС‚РѕР±СЂР°Р¶РµРЅРёСЏ
					"ADD_PICT_PROP" => "-",	// Р”РѕРїРѕР»РЅРёС‚РµР»СЊРЅР°СЏ РєР°СЂС‚РёРЅРєР° РѕСЃРЅРѕРІРЅРѕРіРѕ С‚РѕРІР°СЂР°
					"LABEL_PROP" => "-",	// РЎРІРѕР№СЃС‚РІРѕ РјРµС‚РѕРє С‚РѕРІР°СЂР°
					"SHOW_DISCOUNT_PERCENT" => "N",	// РџРѕРєР°Р·С‹РІР°С‚СЊ РїСЂРѕС†РµРЅС‚ СЃРєРёРґРєРё
					"SHOW_OLD_PRICE" => "N",	// РџРѕРєР°Р·С‹РІР°С‚СЊ СЃС‚Р°СЂСѓСЋ С†РµРЅСѓ
					"SHOW_CLOSE_POPUP" => "N",	// РџРѕРєР°Р·С‹РІР°С‚СЊ РєРЅРѕРїРєСѓ РїСЂРѕРґРѕР»Р¶РµРЅРёСЏ РїРѕРєСѓРїРѕРє РІРѕ РІСЃРїР»С‹РІР°СЋС‰РёС… РѕРєРЅР°С…
					"MESS_BTN_BUY" => "РљСѓРїРёС‚СЊ",	// РўРµРєСЃС‚ РєРЅРѕРїРєРё "РљСѓРїРёС‚СЊ"
					"MESS_BTN_ADD_TO_BASKET" => "Р’ РєРѕСЂР·РёРЅСѓ",	// РўРµРєСЃС‚ РєРЅРѕРїРєРё "Р”РѕР±Р°РІРёС‚СЊ РІ РєРѕСЂР·РёРЅСѓ"
					"MESS_BTN_COMPARE" => "РЎСЂР°РІРЅРёС‚СЊ",	// РўРµРєСЃС‚ РєРЅРѕРїРєРё "РЎСЂР°РІРЅРёС‚СЊ"
					"MESS_BTN_DETAIL" => "РџРѕРґСЂРѕР±РЅРµРµ",	// РўРµРєСЃС‚ РєРЅРѕРїРєРё "РџРѕРґСЂРѕР±РЅРµРµ"
					"MESS_NOT_AVAILABLE" => "РќРµС‚ РІ РЅР°Р»РёС‡РёРё",	// РЎРѕРѕР±С‰РµРЅРёРµ РѕР± РѕС‚СЃСѓС‚СЃС‚РІРёРё С‚РѕРІР°СЂР°
					"SECTION_URL" => "",	// URL, РІРµРґСѓС‰РёР№ РЅР° СЃС‚СЂР°РЅРёС†Сѓ СЃ СЃРѕРґРµСЂР¶РёРјС‹Рј СЂР°Р·РґРµР»Р°
					"DETAIL_URL" => "",	// URL, РІРµРґСѓС‰РёР№ РЅР° СЃС‚СЂР°РЅРёС†Сѓ СЃ СЃРѕРґРµСЂР¶РёРјС‹Рј СЌР»РµРјРµРЅС‚Р° СЂР°Р·РґРµР»Р°
					"SECTION_ID_VARIABLE" => "SECTION_ID",	// РќР°Р·РІР°РЅРёРµ РїРµСЂРµРјРµРЅРЅРѕР№, РІ РєРѕС‚РѕСЂРѕР№ РїРµСЂРµРґР°РµС‚СЃСЏ РєРѕРґ РіСЂСѓРїРїС‹
					"PRODUCT_QUANTITY_VARIABLE" => "",	// РќР°Р·РІР°РЅРёРµ РїРµСЂРµРјРµРЅРЅРѕР№, РІ РєРѕС‚РѕСЂРѕР№ РїРµСЂРµРґР°РµС‚СЃСЏ РєРѕР»РёС‡РµСЃС‚РІРѕ С‚РѕРІР°СЂР°
					"SEF_MODE" => "Y",	// Р’РєР»СЋС‡РёС‚СЊ РїРѕРґРґРµСЂР¶РєСѓ Р§РџРЈ
					"SEF_RULE" => "",	// РџСЂР°РІРёР»Рѕ РґР»СЏ РѕР±СЂР°Р±РѕС‚РєРё
					"CACHE_TYPE" => "A",	// РўРёРї РєРµС€РёСЂРѕРІР°РЅРёСЏ
					"CACHE_TIME" => "36000000",	// Р’СЂРµРјСЏ РєРµС€РёСЂРѕРІР°РЅРёСЏ (СЃРµРє.)
					"CACHE_GROUPS" => "Y",	// РЈС‡РёС‚С‹РІР°С‚СЊ РїСЂР°РІР° РґРѕСЃС‚СѓРїР°
					"CACHE_FILTER" => "N",	// РљРµС€РёСЂРѕРІР°С‚СЊ РїСЂРё СѓСЃС‚Р°РЅРѕРІР»РµРЅРЅРѕРј С„РёР»СЊС‚СЂРµ
					"ACTION_VARIABLE" => "action",	// РќР°Р·РІР°РЅРёРµ РїРµСЂРµРјРµРЅРЅРѕР№, РІ РєРѕС‚РѕСЂРѕР№ РїРµСЂРµРґР°РµС‚СЃСЏ РґРµР№СЃС‚РІРёРµ
					"PRODUCT_ID_VARIABLE" => "id",	// РќР°Р·РІР°РЅРёРµ РїРµСЂРµРјРµРЅРЅРѕР№, РІ РєРѕС‚РѕСЂРѕР№ РїРµСЂРµРґР°РµС‚СЃСЏ РєРѕРґ С‚РѕРІР°СЂР° РґР»СЏ РїРѕРєСѓРїРєРё
					"PRICE_CODE" => array(	// РўРёРї С†РµРЅС‹
						0 => "BASE",
					),
					"USE_PRICE_COUNT" => "N",	// Р?СЃРїРѕР»СЊР·РѕРІР°С‚СЊ РІС‹РІРѕРґ С†РµРЅ СЃ РґРёР°РїР°Р·РѕРЅР°РјРё
					"SHOW_PRICE_COUNT" => "1",	// Р’С‹РІРѕРґРёС‚СЊ С†РµРЅС‹ РґР»СЏ РєРѕР»РёС‡РµСЃС‚РІР°
					"PRICE_VAT_INCLUDE" => "Y",	// Р’РєР»СЋС‡Р°С‚СЊ РќР”РЎ РІ С†РµРЅСѓ
					"CONVERT_CURRENCY" => "N",	// РџРѕРєР°Р·С‹РІР°С‚СЊ С†РµРЅС‹ РІ РѕРґРЅРѕР№ РІР°Р»СЋС‚Рµ
					"BASKET_URL" => "/personal/basket.php",	// URL, РІРµРґСѓС‰РёР№ РЅР° СЃС‚СЂР°РЅРёС†Сѓ СЃ РєРѕСЂР·РёРЅРѕР№ РїРѕРєСѓРїР°С‚РµР»СЏ
					"USE_PRODUCT_QUANTITY" => "N",	// Р Р°Р·СЂРµС€РёС‚СЊ СѓРєР°Р·Р°РЅРёРµ РєРѕР»РёС‡РµСЃС‚РІР° С‚РѕРІР°СЂР°
					"ADD_PROPERTIES_TO_BASKET" => "Y",	// Р”РѕР±Р°РІР»СЏС‚СЊ РІ РєРѕСЂР·РёРЅСѓ СЃРІРѕР№СЃС‚РІР° С‚РѕРІР°СЂРѕРІ Рё РїСЂРµРґР»РѕР¶РµРЅРёР№
					"PRODUCT_PROPS_VARIABLE" => "prop",	// РќР°Р·РІР°РЅРёРµ РїРµСЂРµРјРµРЅРЅРѕР№, РІ РєРѕС‚РѕСЂРѕР№ РїРµСЂРµРґР°СЋС‚СЃСЏ С…Р°СЂР°РєС‚РµСЂРёСЃС‚РёРєРё С‚РѕРІР°СЂР°
					"PARTIAL_PRODUCT_PROPERTIES" => "N",	// Р Р°Р·СЂРµС€РёС‚СЊ РґРѕР±Р°РІР»СЏС‚СЊ РІ РєРѕСЂР·РёРЅСѓ С‚РѕРІР°СЂС‹, Сѓ РєРѕС‚РѕСЂС‹С… Р·Р°РїРѕР»РЅРµРЅС‹ РЅРµ РІСЃРµ С…Р°СЂР°РєС‚РµСЂРёСЃС‚РёРєРё
					"PRODUCT_PROPERTIES" => "",	// РҐР°СЂР°РєС‚РµСЂРёСЃС‚РёРєРё С‚РѕРІР°СЂР°
					"OFFERS_CART_PROPERTIES" => "",	// РЎРІРѕР№СЃС‚РІР° РїСЂРµРґР»РѕР¶РµРЅРёР№, РґРѕР±Р°РІР»СЏРµРјС‹Рµ РІ РєРѕСЂР·РёРЅСѓ
					"ADD_TO_BASKET_ACTION" => "ADD",	// РџРѕРєР°Р·С‹РІР°С‚СЊ РєРЅРѕРїРєСѓ РґРѕР±Р°РІР»РµРЅРёСЏ РІ РєРѕСЂР·РёРЅСѓ РёР»Рё РїРѕРєСѓРїРєРё
					"DISPLAY_COMPARE" => "N",	// Р Р°Р·СЂРµС€РёС‚СЊ СЃСЂР°РІРЅРµРЅРёРµ С‚РѕРІР°СЂРѕРІ
					"ROTATE_TIMER" => "30"
				),
				false
			);?>
		</div>
	</div>
</div>