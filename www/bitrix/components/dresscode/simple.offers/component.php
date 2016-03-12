<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)	die();?>
<?
	if (CModule::IncludeModule("catalog")){
	
		global $arrFilter;
		
		if(!empty($arParams["PROP_NAME"]) && $arParams["PROP_VALUE"]){

			$arrFilter["PROPERTY_".$arParams["PROP_NAME"]] = $arParams["PROP_VALUE"];

			$arParams["FILTER_NAME"] = "arrFilter";
			if(empty($arParams["CURRENCY_ID"])){
				$arParams["CURRENCY_ID"] = CCurrency::GetBaseCurrency();
				$arParams['CONVERT_CURRENCY'] = "Y";
			}
		}else{
			$arResult["SHOW_TEMPLATE"] = false;
		}

		$this->IncludeComponentTemplate();
	}
?>

