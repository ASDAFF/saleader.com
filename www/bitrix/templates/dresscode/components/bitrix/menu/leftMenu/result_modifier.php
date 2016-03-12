<?		
	global $USER;
	$obCache = new CPHPCache();
	if($obCache->InitCache(360000000, $USER->GetGroups(), "/")){
	   $arResult = $obCache->GetVars();
	}
	elseif($obCache->StartDataCache()){
		if(!empty($arResult)){
			
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
					$properties = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "PROPERTY_TYPE" => "L", "IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"]));
					while ($prop_fields = $properties->GetNext()){
						if($prop_fields["SORT"] <= 100){
							$propValues = array();
							$property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC", "DEF" => "DESC"), Array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "CODE" => $prop_fields["CODE"]));
							while($enum_fields = $property_enums->GetNext()){
								if(!empty($enum_fields["VALUE"])){
									$propValues[$enum_fields["EXTERNAL_ID"]] = array(
										"VALUE"  => $enum_fields["VALUE"],
										"SELECTED"  => N,
										"DISABLED"  => N,
									);
								}
							}
							if(!empty($propValues)){
								$arResult["PROPERTIES"][$prop_fields["CODE"]] = array_merge(
									$prop_fields, array("VALUES" => $propValues)
								);
							}
						}
					}
				}

				$res = CIBlockElement::GetList(
					array(),
					array(
						"ACTIVE" => Y,
						"PROPERTY_SHOW_MENU_VALUE" => Y
					),false, false,
					array(
						"ID",
						"NAME",
						"IBLOCK_ID",
						"DETAIL_PICTURE",
						"DETAIL_PAGE_URL",
						"CATALOG_QUANTITY",
						"IBLOCK_SECTION_ID",
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
							"PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arProduct["ID"]
						);

		 				if($OPTION_ADD_CART == N){
							$arFilter[">CATALOG_QUANTITY"] = 0;
						}

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
											"VALUE"    => $arKeys[0]
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
															"VALUE"    => $keyValue
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
									if($arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE_XML_ID"] != $arNextClean["VALUE"]){
										$active = false;
										break(1);
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

								}
							}

						}
					}
						$arResult["PRODUCTS"][$arResult["SECTIONS"][$arProduct["IBLOCK_SECTION_ID"]]][] = $arProduct;

				}

			}

		}
	   $obCache->EndDataCache($arResult);
	}
	


?>
