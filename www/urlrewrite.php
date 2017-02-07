<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^={\$arResult[\"FOLDER\"].\$arResult[\"URL_TEMPLATES\"][\"smart_filter\"]}\\??(.*)#",
		"RULE" => "&\$1",
		"ID" => "bitrix:catalog.smart.filter",
		"PATH" => "/bitrix/templates/dresscode/components/bitrix/catalog/.default/section.php",
	),
	array(
		"CONDITION" => "#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#",
		"RULE" => "alias=\$1",
		"ID" => "bitrix:im.router",
		"PATH" => "/desktop_app/router.php",
	),
	array(
		"CONDITION" => "#^/bitrix/services/ymarket/#",
		"RULE" => "",
		"ID" => "",
		"PATH" => "/bitrix/services/ymarket/index.php",
	),
	array(
		"CONDITION" => "#^/acrit.exportpro/(.*)#",
		"RULE" => "path=\$1",
		"ID" => "",
		"PATH" => "/acrit.exportpro/index.php",
	),
	array(
		"CONDITION" => "#^/online/(/?)([^/]*)#",
		"RULE" => "",
		"ID" => "bitrix:im.router",
		"PATH" => "/desktop_app/router.php",
	),
	array(
		"CONDITION" => "#^/personal/order/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.order",
		"PATH" => "/personal/order/index.php",
	),
	array(
		"CONDITION" => "#^/about/contacts/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/about/contacts/index.php",
	),
	array(
		"CONDITION" => "#^/collection/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/collection/index.php",
	),
	array(
		"CONDITION" => "#^/services/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/services/index.php",
	),
	array(
		"CONDITION" => "#^/catalog/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/catalog/index.php",
	),
	array(
		"CONDITION" => "#^/survey/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/survey/index.php",
	),
	array(
		"CONDITION" => "#^/brands/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/brands/index.php",
	),
	array(
		"CONDITION" => "#^/stores/#",
		"RULE" => "",
		"ID" => "bitrix:catalog.store",
		"PATH" => "/stores/index.php",
	),
	array(
		"CONDITION" => "#^/store/#",
		"RULE" => "",
		"ID" => "bitrix:catalog.store",
		"PATH" => "/store/index.php",
	),
	array(
		"CONDITION" => "#^/stock/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/stock/index.php",
	),
	array(
		"CONDITION" => "#^/news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/news/index.php",
	),

);

?>