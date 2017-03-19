<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->AddViewContent("hiddenZoneClassEl",'hiddenZone');
$APPLICATION->AddViewContent("hiddenZoneClass",'hiddenZone');
	$APPLICATION->SetTitle("Склады");?><h1>Склады</h1><?$APPLICATION->IncludeComponent("bitrix:catalog.store", ".default", Array(
	"CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"CACHE_TYPE" => "A",	// Тип кеширования
		"COMPONENT_TEMPLATE" => ".default",
		"MAP_TYPE" => "1",	// Тип используемой карты
		"PHONE" => "Y",	// Отображать телефон
		"SCHEDULE" => "Y",	// Отображать график работы
		"SEF_FOLDER" => "/stores/",	// Каталог ЧПУ (относительно корня сайта)
		"SEF_MODE" => "Y",	// Включить поддержку ЧПУ
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"TITLE" => "Список складов с подробной информацией",	// Заголовок страницы
		"SEF_URL_TEMPLATES" => array(
			"liststores" => "index.php",
			"element" => "#store_id#/",
		)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>