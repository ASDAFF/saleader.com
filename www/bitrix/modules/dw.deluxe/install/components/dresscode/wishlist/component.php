<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)	die();?>
<?
	global $arrFilter;
	
	if(!empty($_SESSION["WISHLIST_LIST"]["ITEMS"])){
		$arrFilter["ID"] = $_SESSION["WISHLIST_LIST"]["ITEMS"];
	}else{
		$arResult["SHOW_TEMPLATE"] = false;
	}
	
	$arParams["FILTER_NAME"] = "arrFilter";
	// $arParams["CURRENCY_ID"] = CCurrency::GetBaseCurrency();
	// $arParams['CONVERT_CURRENCY'] = "Y";
	
	$this->IncludeComponentTemplate();
?>

