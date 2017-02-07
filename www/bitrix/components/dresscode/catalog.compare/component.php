<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){
		die();
	}
	if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("sale"))
	return;
?>
<?	
	$arResult   = array();
	$arIblock   = array();
	$arProps    = array();
	$arPropsTmp = array();

	$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
	$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

	$dbPriceType = CCatalogGroup::GetList(
        array("SORT" => "ASC"),
        array("BASE" => "Y")
	);

	while ($arPriceType = $dbPriceType->Fetch()){
	    $OPTION_BASE_PRICE = $arPriceType["ID"];
	}

	if(!empty($_SESSION["COMPARE_LIST"]["ITEMS"])){
		
		$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "CATALOG_QUANTITY");
		$arFilter = Array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $_SESSION["COMPARE_LIST"]["ITEMS"]);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		
		while($ob = $res->GetNextElement()){ 
			$arElementInfo = $ob->GetFields();
			$skuElementID = CCatalogSku::GetProductInfo($arElementInfo["ID"]);
			
			if(!empty($skuElementID)){
				$resMore = CIBlockElement::GetList(Array(), array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $skuElementID), false, false, $arSelect)->GetNextElement();
				$arMore = $resMore->GetFields();
				$arIblock[$arMore["IBLOCK_ID"]] = $arMore["ID"];
				if(empty($arElementInfo["DETAIL_PICTURE"]) && !empty($arMore["DETAIL_PICTURE"])){
					$arElementInfo["DETAIL_PICTURE"] = $arMore["DETAIL_PICTURE"];
				}
			}

			$dbPrice = CPrice::GetList(
		        array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", "SORT" => "ASC"),
		        array(
		        	"PRODUCT_ID" => $arElementInfo["ID"],
		        	"CATALOG_GROUP_ID" => $OPTION_BASE_PRICE
		        ),
		        false,
		        false,
		        array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
			);
			while ($arPrice = $dbPrice->Fetch()){
			    $arDiscounts = CCatalogDiscount::GetDiscountByPrice(
		            $arPrice["ID"],
		            $USER->GetUserGroupArray(),
		            "N",
		            SITE_ID
			    );
			    $arElementInfo["TMP_PRICE"] = $arElementInfo["PRICE"] = CCatalogProduct::CountPriceWithDiscount(
		            $arPrice["PRICE"],
		            $arPrice["CURRENCY"],
		            $arDiscounts
			    );
			
			    $arElementInfo["OLD_PRICE"] = ($arPrice["PRICE"] != $arElementInfo["PRICE"] ? CurrencyFormat(CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY),$OPTION_CURRENCY) : 0);
			    $arElementInfo["PRICE"] = CurrencyFormat(CCurrencyRates::ConvertCurrency($arElementInfo["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY),$OPTION_CURRENCY);
			
			}
			
			if(empty($arElementInfo["TMP_PRICE"])){
				$arElementInfo["SKU"] = CCatalogSKU::IsExistOffers($arElementInfo["ID"]);
				if($arElementInfo["SKU"]){
					$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arElementInfo["IBLOCK_ID"]);
					if (is_array($SKU_INFO)){  
						$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arElementInfo["ID"], "ACTIVE" => Y), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY")); 
						while($arSku = $rsOffers->GetNext()){
							$arSkuPrice = CCatalogProduct::GetOptimalPrice($arSku["ID"], 1, $USER->GetUserGroupArray());
							if(!empty($arSkuPrice)){
								$arElementInfo["SKU_PRODUCT"][] = $arSku + $arSkuPrice;
							}

							if($arElementInfo["PRICE"] > $arSkuPrice["DISCOUNT_PRICE"] || empty($arElementInfo["PRICE"])){
								$arElementInfo["PRICE"] = $arSkuPrice["DISCOUNT_PRICE"];
								if(!empty($arSkuPrice["RESULT_PRICE"]["DISCOUNT"])){
									$arElementInfo["SKU_DISCOUNT_PRICE"] = CurrencyFormat($arSkuPrice["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);
								}
							}
						
							$arResult["SKU_PRICES"][] = $arSkuPrice["DISCOUNT_PRICE"];
								
							if($arSku["CATALOG_QUANTITY"] > 0){
								$arElementInfo["CATALOG_QUANTITY"] = $arSku["CATALOG_QUANTITY"];
							}

						}

						if(min($arResult["SKU_PRICES"]) != max($arResult["SKU_PRICES"])){
							$arElementInfo["SKU_SHOW_FROM"] = true;
						}

						$arElementInfo["PRICE"] = CurrencyFormat($arElementInfo["PRICE"], $OPTION_CURRENCY);
					}
				}
			}

			//комплекты
			$arElementInfo["COMPLECT"] = array();
			$arComplectID = array();

			$rsComplect = CCatalogProductSet::getList(
				array("SORT" => "ASC"),
				array(
					"TYPE" => 1,
					"OWNER_ID" => $arElementInfo["ID"],
					"!ITEM_ID" => $arElementInfo["ID"]
				),
				false,
				false,
				array("*")
			);

			while ($arComplectItem = $rsComplect->Fetch()) {
				$arElementInfo["COMPLECT"]["ITEMS"][$arComplectItem["ITEM_ID"]] = $arComplectItem;
				$arComplectID[$arComplectItem["ITEM_ID"]] = $arComplectItem["ITEM_ID"];
			}

			if(!empty($arComplectID)){

				$arElementInfo["COMPLECT"]["RESULT_PRICE"] = 0;
				$arElementInfo["COMPLECT"]["RESULT_BASE_DIFF"] = 0;
				$arElementInfo["COMPLECT"]["RESULT_BASE_PRICE"] = 0;

				$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_MEASURE");
				$arFilter = Array("ID" => $arComplectID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
				$rsComplectProducts = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
				while($obComplectProducts = $rsComplectProducts->GetNextElement()){
					
					$complectProductFields = $obComplectProducts->GetFields();
					$complectProductFields["PRICE"] = CCatalogProduct::GetOptimalPrice($complectProductFields["ID"], 1, $USER->GetUserGroupArray());
					$complectProductFields["PRICE"]["DISCOUNT_PRICE"] = $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arElementInfo["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
					$complectProductFields["PRICE"]["DISCOUNT_PRICE"] -= $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arElementInfo["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["DISCOUNT_PERCENT"] / 100;
					$complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] * $arElementInfo["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
					$complectProductFields["PRICE"]["PRICE_DIFF"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] - $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
					$complectProductFields["PRICE"]["BASE_PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);
					$complectProductFields["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
					$arElementInfo["COMPLECT"]["RESULT_PRICE"] += $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
					$arElementInfo["COMPLECT"]["RESULT_BASE_PRICE"] += $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"];
					$arElementInfo["COMPLECT"]["RESULT_BASE_DIFF"] += $complectProductFields["PRICE"]["PRICE_DIFF"];

					$complectProductFields = array_merge(
						$arElementInfo["COMPLECT"]["ITEMS"][$complectProductFields["ID"]], 
						$complectProductFields
					);
					
					$arElementInfo["COMPLECT"]["ITEMS"][$complectProductFields["ID"]] = $complectProductFields;

				}

				$arElementInfo["COMPLECT"]["RESULT_PRICE_FORMATED"] = CurrencyFormat($arElementInfo["COMPLECT"]["RESULT_PRICE"], $OPTION_CURRENCY);
				$arElementInfo["COMPLECT"]["RESULT_BASE_DIFF_FORMATED"] = CurrencyFormat($arElementInfo["COMPLECT"]["RESULT_BASE_DIFF"], $OPTION_CURRENCY);
				$arElementInfo["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"] = CurrencyFormat($arElementInfo["COMPLECT"]["RESULT_BASE_PRICE"], $OPTION_CURRENCY); 

				//set price
				$arElementInfo["PRICE"] = $arElementInfo["COMPLECT"]["RESULT_PRICE_FORMATED"];
				if($arElementInfo["COMPLECT"]["RESULT_BASE_DIFF"] > 0){
					$arElementInfo["OLD_PRICE"] = $arElementInfo["COMPLECT"]["RESULT_BASE_PRICE"];
				}

			}

			$arButtons = CIBlock::GetPanelButtons(
				$arElementInfo["IBLOCK_ID"],
				$arElementInfo["ID"],
				$arElementInfo["ID"],
				array("SECTION_BUTTONS" => false, 
					  "SESSID" => false, 
					  "CATALOG" => true
				)
			);

			$arElementInfo["ADDCART"] = $OPTION_ADD_CART == "Y" ? true : $arElementInfo["CATALOG_QUANTITY"] > 0;
			$arElementInfo["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arElementInfo["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
			$arElementInfo["PICTURE"] = CFile::ResizeImageGet($arElementInfo["DETAIL_PICTURE"], array("width" => 200, 'height' => 140), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
			$arElementInfo["CAN_BUY"] = $OPTION_ADD_CART == "Y" ? true : false;
			$arResult["ITEMS"][$arElementInfo["ID"]] = $arElementInfo;
			$arPropsTmp[$arElementInfo["ID"]] = !empty($resMore) ? $ob->GetProperties() + $resMore->GetProperties() : $ob->GetProperties();
			$arIblock[$arElementInfo["IBLOCK_ID"]] = $arElementInfo["ID"];

		}

		foreach ($arIblock as $ibl => $val) {
			$res = CIBlockProperty::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $ibl));
			while ($arProp = $res->GetNext()){
				if($arProp["SORT"] <= 5000 && $arProp["MULTIPLE"] != "Y"){
					$arProp["NAME"] = preg_replace("/\[.*\]/", "", trim($arProp["NAME"]));
					$arProps[$arProp["CODE"]] = $arProp;
				}
			}
		}

		foreach ($arPropsTmp as $elementID => $arElementProps) {
			foreach ($arElementProps as $propCODE => $arProp) {
				if(!empty($arProp["VALUE"]) && $arProp["SORT"] <= 5000 && $arProp["MULTIPLE"] != "Y"){
					$arResult["PROPERTIES"][$propCODE] = $arProps[$propCODE];
				}
			}
		}
		
		if(!empty($arResult["PROPERTIES"])){
			foreach ($arResult["PROPERTIES"] as $propCODE => $arProp) {
				foreach ($arResult["ITEMS"] as $elementID => $arElement) {
					$arResult["ITEMS"][$elementID]["PROPERTIES"][$propCODE] = CIBlockFormatProperties::GetDisplayValue(array(), $arPropsTmp[$elementID][$propCODE], "catalog_out");
				}
			}
		}
	
	}

	$this->IncludeComponentTemplate();
?>