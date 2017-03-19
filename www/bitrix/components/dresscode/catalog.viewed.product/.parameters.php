<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")){
	$arComponentParameters = array(
		"PARAMETERS" => array(
			"CACHE_TIME" => Array("DEFAULT" => "1285912"),
			"PRODUCT_ID" => Array(
				"DEFAULT" => "",
				"NAME" => GetMessage("PRODUCT_ID_LABEL")
			)
		)
	);
}
?>