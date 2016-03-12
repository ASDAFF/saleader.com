<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
		die();

		if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("sale"))
			return false;

		if (!isset($arParams["CACHE_TIME"])){
			$arParams["CACHE_TIME"] = 1285912;
		}

		$cacheID = $USER->GetGroups();

		if(!empty($arParams["PAGE"])){
			$cacheID .= "/".$arParams["PAGE"];
			$arParams["ELEMENTS_COUNT"] = $arParams["ELEMENTS_COUNT"] * $arParams["PAGE"];
		}

		if(!empty($arParams["GROUP_ID"])){
			
			$cacheID .= "/".$arParams["GROUP_ID"];
			$arParams["PROP_VALUE"] = array(
				$arParams["GROUP_ID"]
			);

		}

		if(!empty($arParams["AJAX"])){
			$cacheID .= "/".$arParams["AJAX"];
		}

		if ($this->StartResultCache($arParams["CACHE_TIME"], $cacheID)){

			$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
			$OPTION_CURRENCY  = $arResult["CURRENCY"] = CCurrency::GetBaseCurrency();
			$arProductProperties = array();

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

			if(!empty($arParams["PROP_VALUE"])){
			
				$firstIter = 1;

				foreach ($arParams["PROP_VALUE"] as $ipr => $PROP_ID) {
					
					$arSelect = Array("ID", "NAME", "IBLOCK_ID", "DETAIL_PICTURE", "CATALOG_QUANTITY", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
					$arFilter = Array("IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"], "PROPERTY_".$arParams["PROP_NAME"] => $PROP_ID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
					$res = CIBlockElement::GetList(array($arParams["SORT_PROPERTY_NAME"] => $arParams["SORT_VALUE"]), $arFilter, false, array("nPageSize" => $arParams["ELEMENTS_COUNT"]), $arSelect);

					$PROP_ID = is_array($PROP_ID) ? "all" : $PROP_ID; // 

					while($ob = $res->GetNextElement()){
						$arFields = $ob->GetFields();
						$arProductProperties = $ob->GetProperties();

						foreach ($arProductProperties as $isp => $arProperty) {
							if($arProperty["PROPERTY_TYPE"] == "E" || $arProperty["PROPERTY_TYPE"] == "S" || $arProperty["PROPERTY_TYPE"] == "N"){
								$arProductProperties[$isp] = CIBlockFormatProperties::GetDisplayValue($arFields, $arProductProperties[$isp], "catalog_out");
							}
						}

						if(!empty($arProductProperties["MORE_PHOTO"]["VALUE"])){
							foreach ($arProductProperties["MORE_PHOTO"]["VALUE"] as $impr => $arMorePhoto) {
								$arFields["MORE_PHOTO"][] = CFile::ResizeImageGet($arMorePhoto, array("width" => 30, "height" => 40), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
							}
						}

						$arFields["PRICE"] = CCatalogProduct::GetOptimalPrice($arFields["ID"], 1, $USER->GetUserGroupArray());
						$arFields["CAN_BUY"] = $OPTION_ADD_CART == "Y" ? true : false;
						$arFields["SKU_INFO"] = $SKU_INFO;

						$arFields["SKU"] = CCatalogSKU::IsExistOffers($arFields["ID"]);
						if($arFields["SKU"]){
							$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arFields["IBLOCK_ID"]);
							if (is_array($SKU_INFO)){
								$rsOffers = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arFields["ID"], "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY")); 
								while($arSku = $rsOffers->GetNextElement()){
									
									$arSkuFields = $arSku->GetFields();
									$arSkuProperties = $arSku->GetProperties();

									$arSkuFields["PRICE"] = CCatalogProduct::GetOptimalPrice($arSkuFields["ID"], 1, $USER->GetUserGroupArray());
									
									if(!empty($arSkuFields["PRICE"])){
										$arFields["SKU_PRODUCT"][] = array_merge($arSkuFields, array("PROPERTIES" => $arSkuProperties));
									}
									
									$arFields["SKU_PRICES"][] = $arSkuPrice["DISCOUNT_PRICE"];
								}

								$arFields["ADDSKU"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
							}
						}

						$arButtons = CIBlock::GetPanelButtons(
							$arFields["IBLOCK_ID"],
							$arFields["ID"],
							$arFields["IBLOCK_SECTION_ID"],
							array("SECTION_BUTTONS" => true, 
								  "SESSID" => true, 
								  "CATALOG" => true
							)
						);

						$arFields["ADDCART"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
						$arFields["COMPARE"] = false;
						$arFields["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
						$arFields["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
						$arFields["IMG"] = CFile::ResizeImageGet($arFields["DETAIL_PICTURE"], array("width" => $arParams["PICTURE_WIDTH"], "height" => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);

						if(!empty($arFields["SKU_PRODUCT"]) && !empty($arResult["PROPERTIES"])){
							$arFields["SKU_PROPERTIES"] = $arResult["PROPERTIES"];
							foreach ($arFields["SKU_PROPERTIES"] as $ip => $arProp) {
								foreach ($arProp["VALUES"] as $ipv => $arPropValue) {
									$find = false;;
									foreach ($arFields["SKU_PRODUCT"] as $ipo => $arOffer) {
										if($arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"] == $arPropValue["VALUE"]){
											$find = true;
											break(1);
										}
									}
									if(!$find){
										unset($arFields["SKU_PROPERTIES"][$ip]["VALUES"][$ipv]);
									}
								}
							}

							// first display

							$arPropClean = array();
							$iter = 0;

							foreach ($arFields["SKU_PROPERTIES"] as $ip => $arProp) {
								if(!empty($arProp["VALUES"])){
									$arKeys = array_keys($arProp["VALUES"]);
									$selectedUse = false;
									if($iter === 0){
										$arFields["SKU_PROPERTIES"][$ip]["VALUES"][$arKeys[0]]["SELECTED"] = Y;
										$arPropClean[$ip] = array(
											"PROPERTY" => $ip,
											"VALUE"    => $arKeys[0]
										);
									}else{
										foreach ($arKeys as $key => $keyValue) {
											$disabled = true;
											$checkValue = $arFields["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["VALUE"];
											foreach ($arFields["SKU_PRODUCT"] as $io => $arOffer) {
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
														$arFields["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["SELECTED"] = Y;
														$arPropClean[$ip] = array(
															"PROPERTY" => $ip,
															"VALUE"    => $keyValue
														);
														break(1);
													}
												}
											}
											if($disabled){
												$arFields["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["DISABLED"] = "Y";
											}
										}
									}
									$iter++;
								}
							}

							if(!empty($arFields["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE])){
								foreach ($arFields["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"] as $ic => $arProperty) {
									foreach ($arFields["SKU_PRODUCT"] as $io => $arOffer) {
										if($arOffer["PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUE"] == $arProperty["VALUE"]){
											if(!empty($arOffer["DETAIL_PICTURE"])){
												$arPropertyFile = CFile::GetFileArray($arOffer["DETAIL_PICTURE"]);
												$arPropertyImage = CFile::ResizeImageGet($arPropertyFile, array('width' => 30, 'height' => 30), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
												$arFields["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"][$ic]["IMAGE"] = $arPropertyImage;
												break(1);
											}
										}
									}
								}
							}

							foreach ($arFields["SKU_PRODUCT"] as $ir => $arOffer) {
								$active = true;
								foreach ($arPropClean as $ic => $arNextClean) {
									if($arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE_XML_ID"] != $arNextClean["VALUE"]){
										$active = false;
										break(1);
									}
								}
								if($active){

									if(!empty($arOffer["DETAIL_PICTURE"])){
										$arFields["IMG"] = CFile::ResizeImageGet($arOffer["DETAIL_PICTURE"], array('width' => $arParams["PICTURE_WIDTH"], 'height' => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
									}

									if(!empty($arOffer["NAME"])){
										$arFields["NAME"] = $arOffer["NAME"];
									}

									if(!empty($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
										foreach ($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $impr => $arMorePhoto) {
											$arFields["MORE_PHOTO"][] = CFile::ResizeImageGet($arMorePhoto, array("width" => 40, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
										}
									}

									$arFields["~ID"] = $arFields["ID"];
									$arFields["ID"] = $arOffer["ID"];
									$arFields["PRICE"]   = CCatalogProduct::GetOptimalPrice($arOffer["ID"], 1, $USER->GetUserGroupArray());
									$arFields["IBLOCK_ID"] = $arOffer["IBLOCK_ID"];
									$arFields["CATALOG_QUANTITY"] = $arOffer["CATALOG_QUANTITY"];
									$arFields["CAN_BUY"] = $OPTION_ADD_CART == "Y" ? true : false;

								}
							}

						}

						$arResult["GROUPS"][$PROP_ID]["ITEMS"][] = array_merge($arFields, array("PROPERTIES" => $arProductProperties));

					}

					if(!empty($arResult["GROUPS"][$PROP_ID]["ITEMS"])){
		
						$db_enum_list = CIBlockProperty::GetPropertyEnum($arParams["PROP_NAME"], Array(), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => $PROP_ID));
						if($ar_enum_list = $db_enum_list->GetNext()){

							if($firstIter == 1){

								$ar_enum_list["SELECTED"] = Y;
								$arResult["FIRST_ITEMS_COUNT"] = count($arResult["GROUPS"][$PROP_ID]["ITEMS"]);
								$arResult["FIRST_ITEMS_GROUP_ID"] = $PROP_ID;
								$arResult["FIRST_ITEMS_ALL_COUNT"] = $res->SelectedRowsCount();

								if($arResult["FIRST_ITEMS_ALL_COUNT"] > $arParams["ELEMENTS_COUNT"]){
									$arParams["~ELEMENTS_COUNT"] = $arParams["ELEMENTS_COUNT"] -1;
								}else{
									$arParams["~ELEMENTS_COUNT"] = $arParams["ELEMENTS_COUNT"];
								}
							
								$arParams["NEXT_ELEMENTS_COUNT"] = $arResult["FIRST_ITEMS_ALL_COUNT"] - $arParams["~ELEMENTS_COUNT"];

								if($arParams["NEXT_ELEMENTS_COUNT"] > $arParams["~ELEMENTS_COUNT"]){
									$arParams["NEXT_ELEMENTS_COUNT"] = $arParams["~ELEMENTS_COUNT"];
								}
		
							}
							
							$ar_enum_list["PROP_NAME"] = $arParams["PROP_NAME"];
							$arResult["PROPERTY_ENUM"][$ar_enum_list["ID"]] = $ar_enum_list; 
							$arResult["GROUPS"][$PROP_ID]["PROPERTY"] = $ar_enum_list;

						}

						$firstIter++;

					}
				
				}

				$rsProperty = CIBlockProperty::GetList(
					Array(), Array(
						"ACTIVE" => "Y",
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"CODE" => $arParams["PROP_NAME"]
					)
				);

				if($hdProperty = $rsProperty->GetNext()){
					$arResult["PROPERTY"] = $hdProperty;
				}

			}

			if($arResult["FIRST_ITEMS_COUNT"] == $arParams["ELEMENTS_COUNT"]){
				$arResult["HIDE_LAST_ELEMENT"] = Y;
				array_pop($arResult["GROUPS"][$arResult["FIRST_ITEMS_GROUP_ID"]]["ITEMS"]);
			}
			
			$this->setResultCacheKeys(array());
			$this->IncludeComponentTemplate();
		}


?>