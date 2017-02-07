<?if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$boolCatalog = \Bitrix\Main\Loader::includeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

if($arCurrentValues["USE_REVIEW"] == "Y"){
	$arIBlockReview = array();
	$rsIBlockReview = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["REVIEW_IBLOCK_TYPE"], "ACTIVE" => "Y"));
	while($arRew = $rsIBlockReview->Fetch()){
		$arIBlockReview[$arRew["ID"]] = "[".$arRew["ID"]."] ".$arRew["NAME"];
	}
}

if($arCurrentValues["USE_REVIEW"] == "Y"){
	$arTemplateParameters['REVIEW_IBLOCK_TYPE'] = array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("IBLOCK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y",
	);

	$arTemplateParameters['REVIEW_IBLOCK_ID'] = array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("IBLOCK_IBLOCK"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlockReview,
		"REFRESH" => "Y",
	);

	$arTemplateParameters["HIDE_AVAILABLE_TAB"] = array(
		"NAME" => GetMessage("HIDE_AVAILABLE_TAB"),
		"TYPE" => "CHECKBOX",
		"PARENT" => "BASE",
		"DEFAULT" =>"N",
		"VALUE" => "Y"
	);
}

$arTemplateParameters["HIDE_MEASURES"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("HIDE_MEASURES"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y"
);

$arTemplateParameters["SHOW_SECTION_BANNER"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("SHOW_SECTION_BANNER"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y"
);


?>