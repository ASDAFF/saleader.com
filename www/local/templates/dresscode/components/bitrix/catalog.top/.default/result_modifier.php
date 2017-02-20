<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
	
	$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
	$OPTION_PRICE_TAB = COption::GetOptionString("catalog", "show_catalog_tab_with_offers");
	$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

	if(!empty($arResult["ITEMS"])){

		$COLOR_PROPERTY_NANE = "COLOR";
		$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]);

		if(is_array($SKU_INFO)){
			$properties = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "PROPERTY_TYPE" => "L", "IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"]));
			while ($prop_fields = $properties->GetNext()){
				if($prop_fields["SORT"] <= 100){
					$propValues = array();
					$property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC", "DEF" => "DESC"), Array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "CODE" => $prop_fields["CODE"]));
					while($enum_fields = $property_enums->GetNext()){
						$propValues[$enum_fields["EXTERNAL_ID"]] = array(
							"VALUE"  => $enum_fields["VALUE"],
							"SELECTED"  => N,
							"DISABLED"  => N,
						);
					}
					$arResult["PROPERTIES"][$prop_fields["CODE"]] = array_merge(
						$prop_fields, array("VALUES" => $propValues)
					);
				}
			}
		}


		foreach ($arResult["ITEMS"] as $index => $arElement){
			
			$arButtons = CIBlock::GetPanelButtons(
				$arElement["IBLOCK_ID"],
				$arElement["ID"],
				$arElement["ID"],
				array("SECTION_BUTTONS" => false, 
					  "SESSID" => false, 
					  "CATALOG" => true
				)
			);

			$arElement["SKU"] = CCatalogSKU::IsExistOffers($arElement["ID"]);
			if($arElement["SKU"]){
				if(empty($SKU_INFO)){
					$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arElement["IBLOCK_ID"]);
				}
				if (is_array($SKU_INFO)){
					$rsOffers = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arElement["ID"]), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY")); 
					while($arSku = $rsOffers->GetNextElement()){
						
						$arSkuFields = $arSku->GetFields();
						$arSkuProperties = $arSku->GetProperties();

						$arSkuFields["PRICE"] = CCatalogProduct::GetOptimalPrice($arSkuFields["ID"], 1, $USER->GetUserGroupArray());
						
						if(!empty($arSkuFields["PRICE"])){
							$arElement["SKU_PRODUCT"][] = array_merge($arSkuFields, array("PROPERTIES" => $arSkuProperties));
						}
						
						$arElement["SKU_PRICES"][] = $arSkuPrice["DISCOUNT_PRICE"];
					}

					$arElement["ADDSKU"] = $OPTION_ADD_CART === "Y" ? true : $arElement["CATALOG_QUANTITY"] > 0;
					$arElement["SKU_INFO"] = $SKU_INFO;
				}
			}

			$arResult["ITEMS"][$index]["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arResult["ITEMS"][$index]["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
			
			if(!empty($arElement["SKU_PRODUCT"]) && !empty($arResult["PROPERTIES"])){
				$arElement["SKU_PROPERTIES"] = $arResult["PROPERTIES"];
				foreach ($arElement["SKU_PROPERTIES"] as $ip => $arProp) {
					foreach ($arProp["VALUES"] as $ipv => $arPropValue) {
						$find = false;;
						foreach ($arElement["SKU_PRODUCT"] as $ipo => $arOffer) {
							if($arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"] == $arPropValue["VALUE"]){
								$find = true;
								break(1);
							}
						}
						if(!$find){
							unset($arElement["SKU_PROPERTIES"][$ip]["VALUES"][$ipv]);
						}
					}
				}

				// first display

				$arPropClean = array();
				$iter = 0;

				foreach ($arElement["SKU_PROPERTIES"] as $ip => $arProp) {

					$arKeys = array_keys($arProp["VALUES"]);
					$selectedUse = false;
					if($iter === 0){
						$arElement["SKU_PROPERTIES"][$ip]["VALUES"][$arKeys[0]]["SELECTED"] = Y;
						$arPropClean[$ip] = array(
							"PROPERTY" => $ip,
							"VALUE"    => $arKeys[0]
						);
					}else{
						foreach ($arKeys as $key => $keyValue) {
							$disabled = true;
							$checkValue = $arElement["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["VALUE"];
							foreach ($arElement["SKU_PRODUCT"] as $io => $arOffer) {
								if($arOffer["PROPERTIES"][$ip]["VALUE"] == $checkValue){
									$disabled = false; $selected = true;
									foreach ($arPropClean as $ic => $arNextClean) {
										if($arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE_XML_ID"] != $arNextClean["VALUE"]){
											if($ic == $ip){
												break(2);
											}
											$disabled = true;
											$selected = false;
											break(1);
										}
									}
									if($selected && !$selectedUse){
										$selectedUse = true;
										$arElement["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["SELECTED"] = Y;
										$arPropClean[$ip] = array(
											"PROPERTY" => $ip,
											"VALUE"    => $keyValue
										);
										break(1);
									}
								}
							}
							if($disabled){
								$arElement["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["DISABLED"] = "Y";
							}
						}
					}
					$iter++;
				}

				if(!empty($arElement["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE])){
					foreach ($arElement["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"] as $ic => $arProperty) {
						foreach ($arElement["SKU_PRODUCT"] as $io => $arOffer) {
							if($arOffer["PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUE"] == $arProperty["VALUE"]){
								if(!empty($arOffer["DETAIL_PICTURE"])){
									$arPropertyFile = CFile::GetFileArray($arOffer["DETAIL_PICTURE"]);
									$arPropertyImage = CFile::ResizeImageGet($arPropertyFile, array('width' => 30, 'height' => 30), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
									$arElement["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"][$ic]["IMAGE"] = $arPropertyImage;
									break(1);
								}
							}
						}
					}
				}

				foreach ($arElement["SKU_PRODUCT"] as $ir => $arOffer) {
					$active = true;
					foreach ($arPropClean as $ic => $arNextClean) {
						if($arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE_XML_ID"] != $arNextClean["VALUE"]){
							$active = false;
							break(1);
						}
					}
					if($active){

						if(!empty($arOffer["DETAIL_PICTURE"])){
							$arElement["DETAIL_PICTURE"] = $arOffer["DETAIL_PICTURE"];
						}

						if(!empty($arOffer["NAME"])){
							$arElement["NAME"] = $arOffer["NAME"];
						}

						if(!empty($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
							foreach ($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $impr => $arMorePhoto) {
								$arElement["MORE_PHOTO"][] = CFile::ResizeImageGet($arMorePhoto, array("width" => 40, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
							}
						}

						$arElement["~ID"] = $arElement["ID"];
						$arElement["ID"] = $arOffer["ID"];
						$arElement["TMP_PRICE"]   = CCatalogProduct::GetOptimalPrice($arOffer["ID"], 1, $USER->GetUserGroupArray());

						$arElement["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"] = CurrencyFormat($arElement["TMP_PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
						$arElement["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] = $arElement["TMP_PRICE"]["RESULT_PRICE"]["DISCOUNT"];
						$arElement["MIN_PRICE"]["PRINT_VALUE"] = CurrencyFormat($arElement["TMP_PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);

						$arElement["IBLOCK_ID"] = $arOffer["IBLOCK_ID"];

					}
				}

			}

			//комплекты
			$arElement["COMPLECT"] = array();
			$arComplectID = array();

			$rsComplect = CCatalogProductSet::getList(
				array("SORT" => "ASC"),
				array(
					"TYPE" => 1,
					"OWNER_ID" => $arElement["ID"],
					"!ITEM_ID" => $arElement["ID"]
				),
				false,
				false,
				array("*")
			);

			while ($arComplectItem = $rsComplect->Fetch()) {
				$arElement["COMPLECT"]["ITEMS"][$arComplectItem["ITEM_ID"]] = $arComplectItem;
				$arComplectID[$arComplectItem["ITEM_ID"]] = $arComplectItem["ITEM_ID"];
			}

			if(!empty($arComplectID)){

				$arElement["COMPLECT"]["RESULT_PRICE"] = 0;
				$arElement["COMPLECT"]["RESULT_BASE_DIFF"] = 0;
				$arElement["COMPLECT"]["RESULT_BASE_PRICE"] = 0;

				$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_MEASURE");
				$arFilter = Array("ID" => $arComplectID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
				$rsComplectProducts = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
				while($obComplectProducts = $rsComplectProducts->GetNextElement()){
					
					$complectProductFields = $obComplectProducts->GetFields();
					$complectProductFields["PRICE"] = CCatalogProduct::GetOptimalPrice($complectProductFields["ID"], 1, $USER->GetUserGroupArray());
					$complectProductFields["PRICE"]["DISCOUNT_PRICE"] = $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arElement["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
					$complectProductFields["PRICE"]["DISCOUNT_PRICE"] -= $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arElement["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["DISCOUNT_PERCENT"] / 100;
					$complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] * $arElement["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
					$complectProductFields["PRICE"]["PRICE_DIFF"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] - $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
					$complectProductFields["PRICE"]["BASE_PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);
					$complectProductFields["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
					$arElement["COMPLECT"]["RESULT_PRICE"] += $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
					$arElement["COMPLECT"]["RESULT_BASE_PRICE"] += $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"];
					$arElement["COMPLECT"]["RESULT_BASE_DIFF"] += $complectProductFields["PRICE"]["PRICE_DIFF"];

					$complectProductFields = array_merge(
						$arElement["COMPLECT"]["ITEMS"][$complectProductFields["ID"]], 
						$complectProductFields
					);
					
					$arElement["COMPLECT"]["ITEMS"][$complectProductFields["ID"]] = $complectProductFields;

				}

				$arElement["COMPLECT"]["RESULT_PRICE_FORMATED"] = CurrencyFormat($arElement["COMPLECT"]["RESULT_PRICE"], $OPTION_CURRENCY);
				$arElement["COMPLECT"]["RESULT_BASE_DIFF_FORMATED"] = CurrencyFormat($arElement["COMPLECT"]["RESULT_BASE_DIFF"], $OPTION_CURRENCY);
				$arElement["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"] = CurrencyFormat($arElement["COMPLECT"]["RESULT_BASE_PRICE"], $OPTION_CURRENCY); 

				//set price
				$arElement["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"] = $arElement["COMPLECT"]["RESULT_PRICE_FORMATED"];
				$arElement["MIN_PRICE"]["PRINT_VALUE"] = $arElement["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"];
				$arElement["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] = $arElement["COMPLECT"]["RESULT_BASE_DIFF"];

			}

			$arResult["ITEMS"][$index] = $arElement;

		}
	}

?>