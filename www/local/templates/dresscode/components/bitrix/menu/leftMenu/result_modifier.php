<?		
	global $USER;

	CModule::IncludeModule('highloadblock');
	use Bitrix\Highloadblock as HL; 
	use Bitrix\Main\Entity;

	$obCache = new CPHPCache();
	if($obCache->InitCache(3600000000, $USER->GetGroups().SITE_ID."v1", "/")){
	   $arResult = $obCache->GetVars();
	}
	elseif($obCache->StartDataCache()){
		if(!empty($arResult)){

			$arMeasureProductsID = array();
			$i = 0;
			$b = 0;

			foreach($arResult as $arElement){

				if($arElement["DEPTH_LEVEL"] == 1){
					$i++;
					$sectionID = $arElement["PARAMS"]["ID"];
					$IBLOCK_ID = $arElement["PARAMS"]["IBLOCK_ID"];
					$arResult["SECTIONS"][$sectionID] = $sectionID;
					$arResult["ITEMS"][$i] = array(
						"TEXT" => $arElement["TEXT"],
						"LINK" => $arElement["LINK"],
						"ID" => $arElement["PARAMS"]["ID"],
						"SELECTED" => $arElement["SELECTED"],
						"PICTURE" => $arElement["PARAMS"]["PICTURE"],
						"IBLOCK_ID" => $arElement["PARAMS"]["IBLOCK_ID"],
						"ELEMENT_CNT" => $arElement["PARAMS"]["ELEMENT_CNT"]
					);
				}

				elseif($arElement["DEPTH_LEVEL"] == 2){
					$b++;
					$from = $arElement["PARAMS"]["FROM_IBLOCK"] <= 100 ? 1 : 2;
					$arResult["SECTIONS"][$arElement["PARAMS"]["ID"]] = $sectionID;
					$arResult["ITEMS"][$i]["ELEMENTS"][$from][$b] = array(
						"TEXT" => $arElement["TEXT"],
						"LINK" => $arElement["LINK"],
						"SELECTED" => $arElement["SELECTED"],
						"PICTURE" => $arElement["PARAMS"]["PICTURE"],
						"ELEMENT_CNT" => $arElement["PARAMS"]["ELEMENT_CNT"]
					);
				}elseif($arElement["DEPTH_LEVEL"] == 3){
					$arResult["SECTIONS"][$arElement["PARAMS"]["ID"]] = $sectionID;
					$arResult["ITEMS"][$i]["ELEMENTS"][$from][$b]["ELEMENTS"][] = array(
						"TEXT" => $arElement["TEXT"],
						"LINK" => $arElement["LINK"],
						"SELECTED" => $arElement["SELECTED"],
						"ELEMENT_CNT" => $arElement["PARAMS"]["ELEMENT_CNT"]
					);
				}

			}

			if(CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")){

				$COLOR_PROPERTY_NANE = "COLOR";
				$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
				$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);

				if(is_array($SKU_INFO)){
					$properties = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"]));
					while ($prop_fields = $properties->GetNext()){
						if($prop_fields["SORT"] <= 100 && $prop_fields["PROPERTY_TYPE"] == "L"){
							$propValues = array();
							$prop_fields["HIGHLOAD"] = "N";
							$property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC", "DEF" => "DESC"), Array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "CODE" => $prop_fields["CODE"]));
							while($enum_fields = $property_enums->GetNext()){
								$propValues[$enum_fields["EXTERNAL_ID"]] = array(
									"VALUE"  => $enum_fields["VALUE"],
									"DISPLAY_VALUE"  => $enum_fields["VALUE"],
									"SELECTED"  => N,
									"DISABLED"  => N,
									"HIGHLOAD" => N
								);
							}
							$arResult["PROPERTIES"][$prop_fields["CODE"]] = array_merge(
								$prop_fields, array("VALUES" => $propValues)
							);
						}elseif($prop_fields["SORT"] <= 100 && $prop_fields["PROPERTY_TYPE"] == "S" && !empty($prop_fields["USER_TYPE_SETTINGS"]["TABLE_NAME"])){
							$propValues = array();
							$prop_fields["HIGHLOAD"] = "Y";
							$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
						        array("filter" => array(
						            "TABLE_NAME" => $prop_fields["USER_TYPE_SETTINGS"]["TABLE_NAME"]
						        ))
						    )->fetch();
						  
						    if(!empty($hlblock)){
								
								$hlblock_requests = HL\HighloadBlockTable::getById($hlblock["ID"])->fetch();
								$entity_requests = HL\HighloadBlockTable::compileEntity($hlblock_requests);
								$entity_requests_data_class = $entity_requests->getDataClass();

								$main_query_requests = new Entity\Query($entity_requests_data_class);
								$main_query_requests->setSelect(array("*"));
								$result_requests = $main_query_requests->exec();
								$result_requests = new CDBResult($result_requests);

								while ($row_requests = $result_requests->Fetch()) {
									
									if(!empty($row_requests["UF_FILE"])){
			 							$row_requests["UF_FILE"] = CFile::ResizeImageGet($row_requests["UF_FILE"], array("width" => 30, "height" => 30), BX_RESIZE_IMAGE_PROPORTIONAL, false); 
										$hasPicture = true;
									}
									
									$propValues[$row_requests["UF_XML_ID"]] = array(
										"VALUE" => $row_requests["UF_XML_ID"],
										"DISPLAY_VALUE"  => $row_requests["UF_NAME"],
										"SELECTED" => N,
										"DISABLED" => N,
										"UF_XML_ID" => $row_requests["UF_XML_ID"],
										"IMAGE" => $row_requests["UF_FILE"],
										"NAME" => $row_requests["UF_NAME"],
										"HIGHLOAD" => "Y"
									);

								}

								$prop_fields["HIGHLOAD"] = "Y";
								$arResult["PROPERTIES"][$prop_fields["CODE"]] = array_merge(
									$prop_fields, array("VALUES" => $propValues)
								);

								// print_r($requests);

							}
						}
					}
				}

				$res = CIBlockElement::GetList(
					array(),
					array(
						"ACTIVE" => "Y",
						"CATALOG_AVAILABLE" => "Y",
						"!PROPERTY_SHOW_MENU_VALUE" => false,
						"PROPERTY_SHOW_MENU_VALUE" => "Y"
					),false, false,
					array(
						"ID",
						"NAME",
						"IBLOCK_ID",
						"DETAIL_PICTURE",
						"DETAIL_PAGE_URL",
						"CATALOG_QUANTITY",
						"IBLOCK_SECTION_ID",
						"CATALOG_MEASURE"
					)
				);

				while($arProduct = $res->GetNext()){
					$arMap = array();
					$arProduct["PICTURE"] = !empty($arProduct["DETAIL_PICTURE"]) ? CFile::ResizeImageGet($arProduct["DETAIL_PICTURE"], array('width' => 180, 'height' => 240), BX_RESIZE_IMAGE_PROPORTIONAL, true) : array("src" => SITE_TEMPLATE_PATH."/images/empty.png");
					$arProduct["PRICE"]   = CCatalogProduct::GetOptimalPrice($arProduct["ID"], 1, $USER->GetUserGroupArray());

					if($arProduct["CATALOG_QUANTITY"] > 0){
						$arProduct["CAN_BUY"] = "inStock";
					} elseif($OPTION_ADD_CART == "Y" && $arProduct["CATALOG_QUANTITY"] <= 0){
						$arProduct["CAN_BUY"] = "preOrder";
					} elseif($OPTION_ADD_CART == "N" && $arProduct["CATALOG_QUANTITY"] <= 0){
						$arProduct["CAN_BUY"] = "outStock";
					}

		 			if (is_array($SKU_INFO)){

		 				$arFilter = array(
							"ACTIVE" => Y,
							"IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"],
							"PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arProduct["ID"],
						);

		 			// 	if($OPTION_ADD_CART == N){
						// 	$arFilter[">CATALOG_QUANTITY"] = 0;
						// }

		 				$arProduct["SKU_INFO"] = $SKU_INFO;
						$rsOffers = CIBlockElement::GetList(
							array(),
							$arFilter, false, false,
							array(
								"ID",
								"NAME",
								"IBLOCK_ID",
								"DETAIL_PICTURE",
								"CATALOG_QUANTITY",
								"CATALOG_MEASURE"
							)
						);

						while($obOffers = $rsOffers->GetNextElement()){
							$arColumns  = $obOffers->GetFields();
							$arProperties = $obOffers->GetProperties();
							$arProduct["OFFERS"][] = array_merge(
								$arColumns, array("PROPERTIES" => $arProperties)
							);
						}

						if(!empty($arProduct["OFFERS"]) && !empty($arResult["PROPERTIES"])){
							$arProduct["SKU_PROPERTIES"] = $arResult["PROPERTIES"];
							foreach ($arProduct["SKU_PROPERTIES"] as $ip => $arProp) {
								foreach ($arProp["VALUES"] as $ipv => $arPropValue) {
									$find = false;;
									foreach ($arProduct["OFFERS"] as $ipo => $arOffer) {
										if(!empty($arPropValue["VALUE"])){
											if($arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"] == $arPropValue["VALUE"]){
												$find = true;
												break(1);
											}
										}
									}
									if(!$find){
										unset($arProduct["SKU_PROPERTIES"][$ip]["VALUES"][$ipv]);
									}
								}
							}

							// first display

							$arPropClean = array();
							$iter = 0;

							foreach ($arProduct["SKU_PROPERTIES"] as $ip => $arProp) {
								if(!empty($arProp["VALUES"])){
									$arKeys = array_keys($arProp["VALUES"]);
									$selectedUse = false;
									if($iter === 0){
										$arProduct["SKU_PROPERTIES"][$ip]["VALUES"][$arKeys[0]]["SELECTED"] = Y;
										$arPropClean[$ip] = array(
											"PROPERTY" => $ip,
											"VALUE"    => $arKeys[0],
											"HIGHLOAD" => $arProp["HIGHLOAD"]
										);
									}else{
										foreach ($arKeys as $key => $keyValue) {
											$disabled = true;
											$checkValue = $arProduct["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["VALUE"];

											foreach ($arProduct["OFFERS"] as $io => $arOffer) {
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
														$arProduct["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["SELECTED"] = Y;
														$arPropClean[$ip] = array(
															"PROPERTY" => $ip,
															"VALUE"    => $keyValue,
															"HIGHLOAD" => $arProp["HIGHLOAD"]
														);
														break(1);
													}
												}
											}
											if($disabled){
												$arProduct["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["DISABLED"] = "Y";
											}
										}
									}
									$iter++;
								}
							}


							if(!empty($arProduct["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE])){
								foreach ($arProduct["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"] as $ic => $arProperty) {
									foreach ($arProduct["OFFERS"] as $io => $arOffer) {
										if($arOffer["PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUE"] == $arProperty["VALUE"]){
											if(!empty($arOffer["DETAIL_PICTURE"])){
												$arPropertyFile = CFile::GetFileArray($arOffer["DETAIL_PICTURE"]);
												$arPropertyImage = CFile::ResizeImageGet($arPropertyFile, array('width' => 30, 'height' => 30), BX_RESIZE_IMAGE_PROPORTIONAL, false);
												$arProduct["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"][$ic]["IMAGE"] = $arPropertyImage;
												break(1);
											}
										}
									}
								}
							}

							foreach ($arProduct["OFFERS"] as $ir => $arOffer) {
								$active = true;
								foreach ($arPropClean as $ic => $arNextClean) {
									if($arNextClean["HIGHLOAD"] == "Y"){
										if($arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE"] != $arNextClean["VALUE"]){
											$active = false;
											break(1);
										}
									}else{
										if($arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE_XML_ID"] != $arNextClean["VALUE"]){
											$active = false;
											break(1);
										}
									}
								}
								
								if($active){

									if(!empty($arOffer["DETAIL_PICTURE"])){
										$arProduct["PICTURE"] = CFile::ResizeImageGet($arOffer["DETAIL_PICTURE"], array('width' => 180, 'height' => 240), BX_RESIZE_IMAGE_PROPORTIONAL, true);
									}

									if(!empty($arOffer["NAME"])){
										$arProduct["NAME"] = $arOffer["NAME"];
									}

									$arProduct["~ID"] = $arProduct["ID"];
									$arProduct["ID"] = $arOffer["ID"];
									$arProduct["PRICE"]   = CCatalogProduct::GetOptimalPrice($arOffer["ID"], 1, $USER->GetUserGroupArray());
									$arProduct["IBLOCK_ID"] = $arOffer["IBLOCK_ID"];
									$arProduct["CATALOG_QUANTITY"] = $arOffer["CATALOG_QUANTITY"];
									$arProduct["CATALOG_MEASURE"] = $arOffer["CATALOG_MEASURE"];

								}
							}

						}
					}

					//price count
					$dbPrice = CPrice::GetList(
				        array(),
				        array("PRODUCT_ID" => $arProduct["ID"], "CAN_ACCESS" => "Y"),
				        false,
				        false,
				        array("ID")
				    );
					$arProduct["COUNT_PRICES"] = $dbPrice->SelectedRowsCount();

					//комплекты
					$arProduct["COMPLECT"] = array();
					$arComplectID = array();

					$rsComplect = CCatalogProductSet::getList(
						array("SORT" => "ASC"),
						array(
							"TYPE" => 1,
							"OWNER_ID" => $arProduct["ID"],
							"!ITEM_ID" => $arProduct["ID"]
						),
						false,
						false,
						array("*")
					);

					while ($arComplectItem = $rsComplect->Fetch()) {
						$arProduct["COMPLECT"]["ITEMS"][$arComplectItem["ITEM_ID"]] = $arComplectItem;
						$arComplectID[$arComplectItem["ITEM_ID"]] = $arComplectItem["ITEM_ID"];
					}

					if(!empty($arComplectID)){

						$arProduct["COMPLECT"]["RESULT_PRICE"] = 0;
						$arProduct["COMPLECT"]["RESULT_BASE_DIFF"] = 0;
						$arProduct["COMPLECT"]["RESULT_BASE_PRICE"] = 0;

						$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_MEASURE");
						$arFilter = Array("ID" => $arComplectID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
						$rsComplectProducts = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
						while($obComplectProducts = $rsComplectProducts->GetNextElement()){
							
							$complectProductFields = $obComplectProducts->GetFields();
							$complectProductFields["PRICE"] = CCatalogProduct::GetOptimalPrice($complectProductFields["ID"], 1, $USER->GetUserGroupArray());
							$complectProductFields["PRICE"]["DISCOUNT_PRICE"] = $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arProduct["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
							$complectProductFields["PRICE"]["DISCOUNT_PRICE"] -= $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arProduct["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["DISCOUNT_PERCENT"] / 100;
							$complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] * $arProduct["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
							$complectProductFields["PRICE"]["PRICE_DIFF"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] - $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
							$complectProductFields["PRICE"]["BASE_PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);
							$complectProductFields["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
							$arProduct["COMPLECT"]["RESULT_PRICE"] += $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
							$arProduct["COMPLECT"]["RESULT_BASE_PRICE"] += $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"];
							$arProduct["COMPLECT"]["RESULT_BASE_DIFF"] += $complectProductFields["PRICE"]["PRICE_DIFF"];

							$complectProductFields = array_merge(
								$arProduct["COMPLECT"]["ITEMS"][$complectProductFields["ID"]], 
								$complectProductFields
							);
							
							$arProduct["COMPLECT"]["ITEMS"][$complectProductFields["ID"]] = $complectProductFields;

						}

						$arProduct["COMPLECT"]["RESULT_PRICE_FORMATED"] = CurrencyFormat($arProduct["COMPLECT"]["RESULT_PRICE"], $OPTION_CURRENCY);
						$arProduct["COMPLECT"]["RESULT_BASE_DIFF_FORMATED"] = CurrencyFormat($arProduct["COMPLECT"]["RESULT_BASE_DIFF"], $OPTION_CURRENCY);
						$arProduct["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"] = CurrencyFormat($arProduct["COMPLECT"]["RESULT_BASE_PRICE"], $OPTION_CURRENCY); 

						//set price
						$arProduct["PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"] = $arProduct["COMPLECT"]["RESULT_PRICE"];
						if($arProduct["COMPLECT"]["RESULT_BASE_DIFF"] > 0){
							$arProduct["PRICE"]["RESULT_PRICE"]["DISCOUNT"] = $arProduct["COMPLECT"]["RESULT_BASE_DIFF"];
							$arProduct["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $arProduct["COMPLECT"]["RESULT_BASE_PRICE"];
						}

					}

					if(empty($arProduct["COMPLECT"])){
						//Информация о складах
						$rsStore = CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $arProduct["ID"]), false, false, array("ID", "AMOUNT")); 
						while($arNextStore = $rsStore->GetNext()){
							$arProduct["STORES"][] = $arNextStore;
						}
					}

					$arMeasureProductsID[$arProduct["CATALOG_MEASURE"]] = $arProduct["CATALOG_MEASURE"];
					$arResult["PRODUCTS"][$arResult["SECTIONS"][$arProduct["IBLOCK_SECTION_ID"]]][] = $arProduct;

				}


				//коэффициент еденица измерения 
				$rsMeasure = CCatalogMeasure::getList(
					array(),
					array(
						"ID" => $arMeasureProductsID
					),
					false,
					false,
					false
				);
				
				while($arNextMeasure = $rsMeasure->Fetch()) {
					$arResult["MEASURES"][$arNextMeasure["ID"]] = $arNextMeasure;
				}
			
			}

		}
	   $obCache->EndDataCache($arResult);
	}
	


?>
