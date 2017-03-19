<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->AddViewContent("hiddenZoneClassEl",'hiddenZone');
$APPLICATION->AddViewContent("hiddenZoneClass",'hiddenZone');
$APPLICATION->SetTitle("Корзина");
?><h1>Корзина</h1><?$APPLICATION->IncludeComponent("dresscode:sale.basket.basket", "standartOrder", Array(
	
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>