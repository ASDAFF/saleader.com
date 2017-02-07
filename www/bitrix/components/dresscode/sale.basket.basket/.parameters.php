<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(CModule::IncludeModule("iblock")){

	$arComponentParameters = array(
		"GROUPS" => array(
			"BASKET_PICTURE" => array(
				"NAME" => GetMessage("BASKET_PICTURE"),
				"SORT" => "200"
			),
		),
		"PARAMETERS" => array(
			"BASKET_PICTURE_WIDTH" => array(
		         "PARENT" => "BASKET_PICTURE",
		         "NAME" => GetMessage("BASKET_PICTURE_WIDTH"),
		         "TYPE" => "STRING"
			),
			"BASKET_PICTURE_HEIGHT" => array(
		         "PARENT" => "BASKET_PICTURE",
		         "NAME" => GetMessage("BASKET_PICTURE_HEIGHT"),
		         "TYPE" => "STRING"
			),
			"HIDE_MEASURES" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("HIDE_MEASURES"),
				"TYPE" => "CHECKBOX",
				"REFRESH" => "Y"
			),
			"CACHE_TIME" => Array("DEFAULT" => "360000"),
		)
	);

}
?>
