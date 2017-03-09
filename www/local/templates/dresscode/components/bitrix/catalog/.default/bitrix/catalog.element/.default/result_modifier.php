<?
	global $USER;

	CModule::IncludeModule('highloadblock');
	use Bitrix\Highloadblock as HL;
	use Bitrix\Main\Entity;

	function picture_separate_array_push($pictureID, $arPushImage = array()){
		$arPushImage["SMALL_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER);
		$arPushImage["MEDIUM_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => 500, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER);
		$arPushImage["LARGE_IMAGE"] = CFile::GetByID($pictureID)->Fetch();
		$arPushImage["LARGE_IMAGE"]["SRC"] = CFile::GetPath($arPushImage["LARGE_IMAGE"]["ID"]);
		return $arPushImage;
	}

	$cacheId = "catalogElement".$arResult["ID"]."v1".$USER->GetGroups();
	$obCache = new CPHPCache();

	if($obCache->InitCache($arParams["CACHE_TIME"], $cacheId)){
	   $arResult = $obCache->GetVars();
	}

	elseif($obCache->StartDataCache()){

		$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
		$OPTION_PRICE_TAB = COption::GetOptionString("catalog", "show_catalog_tab_with_offers");
		$OPTION_CURRENCY  = $arResult["CURRENCY"] = CCurrency::GetBaseCurrency();


		$arResult["OFFERS_ARRAY_ID"] = array($arResult["ID"] => $arResult["ID"]);
		$arResult["TMP_SKU_PROPERTIES"] = array();
		$arResult["SECTION_PATH_LIST"] = array();
		$arResult["CATALOG_MEASURE"] = array();
		$arResult["SECTIONS_ROW"] = array();
		$arResult["IMAGES"] = array();
		$arResult["FILES"] = array();
		$arComplectID = array();

		$arResult["SHOW_REVIEW_FORM"] = true;

		if(!empty($arResult["PROPERTIES"]["ATT_BRAND"]["VALUE"])){
			$arFilter = Array("ID" => $arResult["PROPERTIES"]["ATT_BRAND"]["VALUE"], "ACTIVE" => "Y");
			$res = CIBlockElement::GetList(Array(), $arFilter, false, false, array("*"));
			if($res = $res->GetNextElement()){
				$arResult["BRAND"] = $res->GetFields();
				$arResult["BRAND"]["PICTURE"] = CFile::ResizeImageGet($arResult["BRAND"]["DETAIL_PICTURE"], array("width" => 250, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false);
			}
		}

		// video and files
		foreach ($arResult["PROPERTIES"] as $ips => $arProperty) {
			if($arProperty["PROPERTY_TYPE"] == "F" && $arProperty["CODE"] != "MORE_PHOTO" && !empty($arProperty["VALUE"])){
				if(is_array($arProperty["VALUE"])){
					foreach ($arProperty["VALUE"] as $ipv => $arPropertyValue) {
						$arTmpFile = CFile::GetByID($arPropertyValue)->Fetch();
						$arTmpFile["PARENT_NAME"] = $arProperty["NAME"];
						$arTmpFile["SRC"] = CFile::GetPath($arTmpFile["ID"]);
						$arResult["FILES"][] = $arTmpFile;
					}
				}else{
					$arTmpFile = CFile::GetByID($arProperty["VALUE"])->Fetch();
					$arTmpFile["PARENT_NAME"] = $arProperty["NAME"];
					$arTmpFile["SRC"] = CFile::GetPath($arTmpFile["ID"]);
					$arResult["FILES"][] = $arTmpFile;
				}
			}elseif($arProperty["CODE"] == "VIDEO" && !empty($arProperty["VALUE"])){
				if(is_array($arProperty["VALUE"])){
					foreach ($arProperty["VALUE"] as $ipv => $arPropertyValue) {
						$arResult["VIDEO"][] = $arPropertyValue;
					}
				}else{
					$arResult["VIDEO"][] = $arProperty["VALUE"];
				}
				unset($arResult["DISPLAY_PROPERTIES"][$ips]);
			}
		}

$arParams["SHOW_REVIEW_FORM"] = true;

$dbPriceType = CCatalogGroup::GetList(
    array("SORT" => "ASC"),
    array()
);

		while ($arPriceType = $dbPriceType->Fetch()){
		    $PRICE_CODES[$arPriceType["NAME"]] = $arPriceType["ID"];
		}

		$arElement = CIblockElement::GetById($arResult["ID"])->GetNext();
		$arResult['DETAIL_PAGE_URL_TMP'] = $arResult['DETAIL_PAGE_URL'];
		$arResult['DETAIL_PAGE_URL'] = $arElement['DETAIL_PAGE_URL'];

		$db_old_groups = CIBlockElement::GetElementGroups(!empty($arParams["USE_SKU"]) ? $arBaseProduct["ID"] : $arResult["ID"], false);

		while($ar_group = $db_old_groups->Fetch()){
			$arSection[$ar_group["DEPTH_LEVEL"]] = $ar_group["ID"];
		}

		!empty($arSection) && krsort($arSection);

		if(!empty($arSection)){
			$arResult["LAST_SECTION"] = array_slice($arSection, 0, 1);
			$res = CIBlockSection::GetByID($arResult["LAST_SECTION"][0]);
			if($arSec = $res->GetNext()){
				$arResult["LAST_SECTION"] = $arSec;
			}
		}

		$nav = CIBlockSection::GetNavChain(false, $arSec["ID"]);
		while($arSectionPath = $nav->GetNext()){
			$arResult["SECTION_PATH_LIST"][] = $arSectionPath;
		}

		$arProductProperties = array();

//related filter

global $relatedFilter;
$relatedFilter = array(
    "ID" => $arResult["PROPERTIES"]["RELATED_PRODUCT"]["VALUE"]
);

$arSelect = Array("ID");
$arFilter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arResult["PROPERTIES"]["RELATED_PRODUCT"]["VALUE"]);
$gRelated = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
$arResult["RELATED_COUNT"] = $gRelated->SelectedRowsCount();

if (intval($arResult["RELATED_COUNT"]) > 0) {
    $arResult["SHOW_RELATED"] = "Y";
}
//similar filter

		if(empty($arResult["IBLOCK_SECTION_ID"]) || !empty($arResult["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"])){

            global $similarFilter;
			$arSelect = Array("ID");
			if(empty($arResult["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"])){
				$db_old_groups = CIBlockElement::GetElementGroups($arResult["ID"], true);
				while($ar_group = $db_old_groups->Fetch()){
					$arResult["SECTIONS_ROW"][$ar_group["DEPTH_LEVEL"]] = $ar_group["ID"];
				}
				krsort($arResult["SECTIONS_ROW"]);
				$similarFilter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "SECTION_ID" => array_slice($arResult["SECTIONS_ROW"], 0, 1), "!ID" => $arResult["OFFERS_ARRAY_ID"]);
				$gSimilar = CIBlockElement::GetList(array(), $similarFilter, false, false, $arSelect);
			}else{
				$similarFilter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arResult["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"]);
				$gSimilar = CIBlockElement::GetList(array(), $similarFilter, false, false, $arSelect);
			}

			$arResult["SIMILAR_COUNT"] = $gSimilar->SelectedRowsCount();

}elseif(!empty($arResult["IBLOCK_SECTION_ID"])) {
    $section_id = $arResult["IBLOCK_SECTION_ID"];
    while ((count($props_array["UF_PARECIDAD"])==0 || !is_array($props_array["UF_PARECIDAD"])) && is_set($section_id)) {
        $section_props = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $section_id),
            true, array("UF_PARECIDAD", "IBLOCK_SECTION_ID"));
        $props_array = $section_props->GetNext();
        $section_id = $props_array['IBLOCK_SECTION_ID'];
    }
    if(is_array($props_array)) {
        $props_array = array_map(function ($item) {
            return "PROPERTY_" . $item;
        }, $props_array["UF_PARECIDAD"]
        );
        $dbRes = CIBlockElement::GetList(
            array('SORT'),
            array("IBLOCKID" => $arParams['IBLOCK_ID'], "ID" => $arResult['ID']),
            false,
            false,
            $props_array
        );
        $El = $dbRes->GetNext();
        $similarFilter = null;
        foreach ($props_array as $prop) {
            $similarFilter[$prop] = $El[$prop . "_ENUM_ID"];
        }
        $similarFilter = array_filter($similarFilter);
        $similarFilter['!ID']=$arResult['ID'];
        $similarFilter['IBLOCK_ID']=$arResult['IBLOCK_ID'];
        $gSimilar = CIBlockElement::GetList(array("RAND" => "ASC"), $similarFilter, false, false, $arSelect);
        $arResult["SIMILAR_COUNT"] = $gSimilar->SelectedRowsCount();
    }
}

$arResult['SIMILAR_FILTER']=$similarFilter;
if (intval($arResult["SIMILAR_COUNT"]) > 0) {
    $arResult["SHOW_SIMILAR"] = "Y";
}
if (!empty($arResult["DISPLAY_PROPERTIES"])) {

    foreach ($arResult["DISPLAY_PROPERTIES"] as $index => $arProp) {
        if ($arProp["CODE"] == "MORE_PROPERTIES") {
            if (!empty($arProp["VALUE"])) {
                foreach ($arProp["VALUE"] as $i => $arValue) {
                    $arResult["DISPLAY_PROPERTIES"][] = array(
                        "CODE" => $arProp["PROPERTY_VALUE_ID"][$i],
                        "SORT" => 5000,
                        "VALUE" => $arProp["DESCRIPTION"][$i],
                        "NAME" => $arValue
                    );
                }
            }
            unset($arResult["DISPLAY_PROPERTIES"][$index]);
        } elseif ($arProp["USER_TYPE"] == "HTML") {
            $arResult["DISPLAY_PROPERTIES"][$index]["VALUE"] = $arProp["~VALUE"]["TEXT"];
        }
    }
}


		$COLOR_PROPERTY_NANE = "COLOR";
		$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]);

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
							"DISPLAY_VALUE" => $enum_fields["VALUE"],
							"SELECTED"  => N,
							"DISABLED"  => N,
							"HIGHLOAD" => N
						);
					}
					$arResult["PROPERTIES_LIST"][$prop_fields["CODE"]] = array_merge(
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
								"DISPLAY_VALUE" => $row_requests["UF_NAME"],
								"UF_XML_ID" => $row_requests["UF_XML_ID"],
								"IMAGE" => $row_requests["UF_FILE"],
								"NAME" => $row_requests["UF_NAME"],
								"HIGHLOAD" => "Y",
								"SELECTED" => N,
								"DISABLED" => N,
							);

						}

						$prop_fields["HIGHLOAD"] = "Y";
						$arResult["PROPERTIES_LIST"][$prop_fields["CODE"]] = array_merge(
							$prop_fields, array("VALUES" => $propValues)
						);

						// print_r($requests);

					}
				}
			}
		}

		$arButtons = CIBlock::GetPanelButtons(
			$arResult["IBLOCK_ID"],
			$arResult["ID"],
			$arResult["ID"],
			array("SECTION_BUTTONS" => false,
				  "SESSID" => false,
				  "CATALOG" => true
			)
		);

		$arResult["SKU"] = CCatalogSKU::IsExistOffers($arResult["ID"]);
		if($arResult["SKU"]){
			if(empty($SKU_INFO)){
				$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arResult["IBLOCK_ID"]);
			}
			if (is_array($SKU_INFO)){
				$arOffersFilter = array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arResult["ID"], "ACTIVE" => "Y");
				if($arParams["HIDE_NOT_AVAILABLE"] == "Y"){
					$arOffersFilter[">CATALOG_QUANTITY"] = 0;
				}
				$rsOffers = CIBlockElement::GetList(array(),$arOffersFilter , false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY", "CATALOG_AVAILABLE"));
				while($arSku = $rsOffers->GetNextElement()){

					$arSkuFields = $arSku->GetFields();
					$arSkuProperties = $arSku->GetProperties();

					$arSkuFields["PRICE"] = CCatalogProduct::GetOptimalPrice($arSkuFields["ID"], 1, $USER->GetUserGroupArray());

					if(!empty($arSkuFields["PRICE"])){
						$arResult["SKU_PRODUCT"][] = array_merge($arSkuFields, array("PROPERTIES" => $arSkuProperties));
					}

					$arResult["SKU_PRICES"][] = $arSkuPrice["DISCOUNT_PRICE"];
				}

				$arResult["ADDSKU"] = $OPTION_ADD_CART === "Y" ? true : $arResult["CATALOG_QUANTITY"] > 0;
				$arResult["SKU_INFO"] = $SKU_INFO;
			}
		}

		$arResult["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arResult["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

		if(!empty($arResult["SKU_PRODUCT"]) && !empty($arResult["PROPERTIES_LIST"])){
			$arResult["SKU_PROPERTIES"] = $arResult["PROPERTIES_LIST"];
			foreach ($arResult["SKU_PROPERTIES"] as $ip => $arProp) {
				foreach ($arProp["VALUES"] as $ipv => $arPropValue) {
					$find = false;;
					foreach ($arResult["SKU_PRODUCT"] as $ipo => $arOffer) {
						if($arProp["HIGHLOAD"] == "Y"){
							if($arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"] == $arPropValue["UF_XML_ID"]){
								$find = true;
								break(1);
							}
						}else{
							if($arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"] == $arPropValue["VALUE"]){
								$find = true;
								break(1);
							}
						}
					}
					if(!$find){
						unset($arResult["SKU_PROPERTIES"][$ip]["VALUES"][$ipv]);
					}
				}
			}

			// first display

			$arPropClean = array();
			$iter = 0;

			foreach ($arResult["SKU_PROPERTIES"] as $ip => $arProp) {
				if(!empty($arProp["VALUES"])){
					$arKeys = array_keys($arProp["VALUES"]);
					$selectedUse = false;
					if($iter === 0){
						$arResult["SKU_PROPERTIES"][$ip]["VALUES"][$arKeys[0]]["SELECTED"] = Y;
						$arPropClean[$ip] = array(
							"PROPERTY" => $ip,
							"VALUE"    => $arKeys[0],
							"HIGHLOAD" => $arProp["HIGHLOAD"]
						);
					}else{
						foreach ($arKeys as $key => $keyValue) {
							$disabled = true;
							$checkValue = $arResult["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["VALUE"];

							foreach ($arResult["SKU_PRODUCT"] as $io => $arOffer) {
								if($arOffer["PROPERTIES"][$ip]["VALUE"] == $checkValue){
									$disabled = false; $selected = true;
									foreach ($arPropClean as $ic => $arNextClean) {
										if($arNextClean["HIGHLOAD"] == "Y" && $arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE"] != $arNextClean["VALUE"] || $arNextClean["HIGHLOAD"] != "Y" && $arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE_XML_ID"] != $arNextClean["VALUE"]){
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
										$arResult["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["SELECTED"] = Y;
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
								$arResult["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["DISABLED"] = "Y";
							}
						}
					}
					$iter++;
				}
			}

			if(!empty($arResult["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE])){
				foreach ($arResult["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"] as $ic => $arProperty) {
					foreach ($arResult["SKU_PRODUCT"] as $io => $arOffer) {
						if($arOffer["PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUE"] == $arProperty["VALUE"]){
							if(!empty($arOffer["DETAIL_PICTURE"])){
								$arPropertyFile = CFile::GetFileArray($arOffer["DETAIL_PICTURE"]);
								$arPropertyImage = CFile::ResizeImageGet($arPropertyFile, array("width" => 30, "height" => 30), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
								$arResult["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"][$ic]["IMAGE"] = $arPropertyImage;
								break(1);
							}
						}
					}
				}
			}


			foreach ($arResult["SKU_PRODUCT"] as $ir => $arOffer) {
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
						array_push($arResult["IMAGES"], picture_separate_array_push($arOffer["DETAIL_PICTURE"]));
					}else{
						if(!empty($arResult["DETAIL_PICTURE"])){
							array_push($arResult["IMAGES"], picture_separate_array_push($arResult["DETAIL_PICTURE"]["ID"]));
						}
					}

					if(!empty($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
						foreach ($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
							array_push($arResult["IMAGES"], picture_separate_array_push($nextPictureID));
						}
					}else{
						if(!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
							foreach ($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
								array_push($arResult["IMAGES"], picture_separate_array_push($nextPictureID));
							}
						}
					}

					if(!empty($arOffer["NAME"])){
						$arResult["NAME"] = $arOffer["NAME"];
					}

					$arResult["~ID"] = $arResult["ID"];

					$arResult["ID"] = $arOffer["ID"];
					$arResult["TMP_PRICE"]   = CCatalogProduct::GetOptimalPrice($arOffer["ID"], 1, $USER->GetUserGroupArray());

					$arResult["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"] = CurrencyFormat($arResult["TMP_PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
					$arResult["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] = $arResult["TMP_PRICE"]["RESULT_PRICE"]["DISCOUNT"];
					$arResult["MIN_PRICE"]["PRINT_VALUE"] = CurrencyFormat($arResult["TMP_PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);

					$arResult["IBLOCK_ID"] = $arOffer["IBLOCK_ID"];
					$arResult["CATALOG_QUANTITY"] = $arOffer["CATALOG_QUANTITY"];
					$arResult["CAN_BUY"] = $arOffer["CATALOG_AVAILABLE"];
					$arResult["CATALOG_MEASURE"][$arOffer["CATALOG_MEASURE"]] = $arOffer["CATALOG_MEASURE"];
					$arResult["CATALOG_MEASURE"] = $arOffer["CATALOG_MEASURE"];

					if(isset($arOffer["PROPERTIES"]["CML2_ARTICLE"]) && empty($arOffer["PROPERTIES"]["CML2_ARTICLE"]["VALUE"])){
						unset($arOffer["PROPERTIES"]["CML2_ARTICLE"]);
					}

					$arResult["TMP_SKU_PROPERTIES"] = $arOffer["PROPERTIES"];

					$arResult["OFFERS_ARRAY_ID"][$arOffer["ID"]] = $arOffer["ID"];

				}
			}

		}

		if(!empty($arResult["TMP_SKU_PROPERTIES"])){
			foreach ($arResult["TMP_SKU_PROPERTIES"] as $ipi => $arNextProperty) {
				$arNextProperty["FROM_SKU"] = "Y";
				$arResult["PROPERTIES"][$ipi] = $arNextProperty;
				$arResult["DISPLAY_PROPERTIES"][$ipi] = CIBlockFormatProperties::GetDisplayValue($arResult, $arNextProperty, "catalog_out");
			}
		}

		if(empty($arResult["IMAGES"])){
			if(!empty($arResult["DETAIL_PICTURE"])){
				array_push($arResult["IMAGES"], picture_separate_array_push($arResult["DETAIL_PICTURE"]["ID"]));
			}

			if(!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
				foreach ($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
					array_push($arResult["IMAGES"], picture_separate_array_push($nextPictureID));
				}
			}
		}

		if(empty($arResult["IMAGES"])){
			array_push($arResult["IMAGES"], array(
				"SMALL_IMAGE" => array(
					"SRC" => SITE_TEMPLATE_PATH."/images/empty.png"
				),
				"MEDIUM_IMAGE" => array(
					"SRC" => SITE_TEMPLATE_PATH."/images/empty.png"
				),
				"LARGE_IMAGE" => array(
					"SRC" => SITE_TEMPLATE_PATH."/images/empty.png"
				)
			));
		}

		$arSelect = Array("ID", "DATE_CREATE", "DETAIL_TEXT", "PROPERTY_DIGNITY", "PROPERTY_SHORTCOMINGS", "PROPERTY_EXPERIENCE", "PROPERTY_GOOD_REVIEW", "PROPERTY_BAD_REVIEW", "PROPERTY_NAME", "PROPERTY_RATING");
		$arFilter = Array("IBLOCK_ID" => $arParams["REVIEW_IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "CODE" => !empty($arParams["USE_SKU"]) ? $arBaseProduct["ID"] : $arResult["ID"]);
		$res = CIBlockElement::GetList(Array("SORT" => "ASC", "CREATED_DATE"), $arFilter, false, false, $arSelect);
		while($ob = $res->GetNextElement()){
			$arResult["REVIEWS"][] = $ob->GetFields();
		}

		$expEnums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID" => $arParams["REVIEW_IBLOCK_ID"], "CODE" => "EXPERIENCE"));
		while($enumValues = $expEnums->GetNext()){
			$arResult["NEW_REVIEW"]["EXPERIENCE"][] = array(
				"ID" => $enumValues["ID"],
				"VALUE" => $enumValues["VALUE"]
			);
		}

		$USER_ID = $USER->GetID();

		if($USER_ID == $arResult["PROPERTIES"]["USER_ID"]["VALUE"] || $USER_ID == false){
			$arResult["SHOW_REVIEW_FORM"] = false;
		}

foreach ($arResult["PROPERTIES"] as $index => $arProp) {

    if ($arProp["CODE"] == "MORE_PROPERTIES") {
        if (!empty($arProp["VALUE"])) {
            foreach ($arProp["VALUE"] as $i => $arValue) {
                $arResult["PROPERTIES"][] = array(
                    "CODE" => $arProp["PROPERTY_VALUE_ID"][$i],
                    "SORT" => 5000,
                    "VALUE" => $arProp["DESCRIPTION"][$i],
                    "NAME" => $arValue
                );
            }
        }
        unset($arResult["PROPERTIES"][$index]);
    } elseif ($arProp["CODE"] == "MORE_PHOTO") {
        unset($arResult["PROPERTIES"][$index]);
    } else if ($arProp["PROPERTY_TYPE"] == "F" && $arProp["SORT"] <= 5000) {
        if (!empty($arProp["VALUE"])) {
            if ($arProp["MULTIPLE"] == "Y") {
                foreach ($arProp["VALUE"] as $ifx => $fileID) {
                    $rsFile = CFile::GetByID($fileID);
                    $arFile = $rsFile->Fetch();
                    $arResult["PROPERTIES"][] = array(
                        "CODE" => $arFile["ID"],
                        "SORT" => 5000,
                        "PROPERTY_TYPE" => "F",
                        "VALUE" => !empty($arProp["DESCRIPTION"][$ifx]) ? '<a href="' . CFile::GetPath($fileID) . '">' . $arProp["DESCRIPTION"][$ifx] . '</a> ' : '<a href="' . CFile::GetPath($fileID) . '">' . $arFile["FILE_NAME"] . '</a> ',
                        "NAME" => $arProp["NAME"]
                    );
                }
            } else {
                $rsFile = CFile::GetByID($arProp["VALUE"]);
                $arFile = $rsFile->Fetch();
                $arResult["PROPERTIES"][] = array(
                    "CODE" => $arFile["ID"],
                    "SORT" => 5000,
                    "PROPERTY_TYPE" => "F",
                    "VALUE" => !empty($arProp["DESCRIPTION"]) ? '<a href="' . CFile::GetPath($fileID) . '">' . $arProp["DESCRIPTION"] . '</a> ' : '<a href="' . CFile::GetPath($arProp["VALUE"]) . '">' . $arFile["FILE_NAME"] . '</a> ',
                    "NAME" => $arProp["NAME"]
                );
            }
        }
        unset($arResult["PROPERTIES"][$index]);
    } elseif ($arProp["USER_TYPE"] == "HTML") {
        $arResult["PROPERTIES"][$index]["VALUE"] = $arProp["~VALUE"]["TEXT"];
    }
}

foreach ($arResult["PROPERTIES"] as $pid => $arPropNext) {
    if ($arPropNext["PROPERTY_TYPE"] == "F" && $arPropNext["SORT"] <= 5000) {
        $arResult["DISPLAY_PROPERTIES"][$pid] = $arPropNext;
    }
}

$i = 0;
$index = 0;
foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp) {
    if (!empty($arProp["VALUE"]) && $arProp["SORT"] <= 5000) {
        if ($i == 5) {
            $index++;
            $i = 0;
        }
        $arResult["TOP_PROPERTIES"][$index][] = $arProp;
        $i++;
    }
}

$arResult["DISPLAY_PROPERTIES_GROUP"] = $arResult["DISPLAY_PROPERTIES"];
usort($arResult["DISPLAY_PROPERTIES_GROUP"], function ($a, $b) {
    return ($a["SORT"] - $b["SORT"]);
});

$rsStore = CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $arResult["ID"]), false, false, array("AMOUNT"));
while ($arStore = $rsStore->Fetch()) {
    if ($arStore["AMOUNT"] > 0) {
        $arResult["SHOW_STORES"] = "Y";
    }
}

		$rsComplect = CCatalogProductSet::getList(
			array("SORT" => "ASC"),
			array(
				"TYPE" => 1,
				"OWNER_ID" => $arResult["ID"],
				"!ITEM_ID" => $arResult["ID"]
			),
			false,
			false,
			array("*")
		);

		while ($arComplectItem = $rsComplect->Fetch()) {
			$arResult["COMPLECT"]["ITEMS"][$arComplectItem["ITEM_ID"]] = $arComplectItem;
			$arComplectID[$arComplectItem["ITEM_ID"]] = $arComplectItem["ITEM_ID"];
		}

		if(!empty($arComplectID)){

			$arResult["COMPLECT"]["RESULT_PRICE"] = 0;
			$arResult["COMPLECT"]["RESULT_BASE_DIFF"] = 0;
			$arResult["COMPLECT"]["RESULT_BASE_PRICE"] = 0;

			$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_MEASURE");
			$arFilter = Array("ID" => $arComplectID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
			$rsComplectProducts = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			while($obComplectProducts = $rsComplectProducts->GetNextElement()){

				$complectProductFields = $obComplectProducts->GetFields();
				$complectProductFields["PRICE"] = CCatalogProduct::GetOptimalPrice($complectProductFields["ID"], 1, $USER->GetUserGroupArray());
				$complectProductFields["PRICE"]["DISCOUNT_PRICE"] = $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
				$complectProductFields["PRICE"]["DISCOUNT_PRICE"] -= $complectProductFields["PRICE"]["DISCOUNT_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["DISCOUNT_PERCENT"] / 100;
				$complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] * $arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]]["QUANTITY"];
				$complectProductFields["PRICE"]["PRICE_DIFF"] = $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] - $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
				$complectProductFields["PRICE"]["BASE_PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);
				$complectProductFields["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($complectProductFields["PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
				$complectProductFields["PICTURE"] = CFile::ResizeImageGet($complectProductFields["DETAIL_PICTURE"], array("width" => 250, "height" => 250), BX_RESIZE_IMAGE_PROPORTIONAL, false);
				$arResult["CATALOG_MEASURE"][$complectProductFields["CATALOG_MEASURE"]] = $complectProductFields["CATALOG_MEASURE"];
				$arResult["COMPLECT"]["RESULT_PRICE"] += $complectProductFields["PRICE"]["DISCOUNT_PRICE"];
				$arResult["COMPLECT"]["RESULT_BASE_PRICE"] += $complectProductFields["PRICE"]["RESULT_PRICE"]["BASE_PRICE"];
				$arResult["COMPLECT"]["RESULT_BASE_DIFF"] += $complectProductFields["PRICE"]["PRICE_DIFF"];

				$complectProductFields = array_merge(
					$arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]],
					$complectProductFields
				);

				//get picture by parent sku product
				if(empty($complectProductFields["PICTURE"]["src"])){
					$skuProductInfo = CCatalogSKU::getProductList($complectProductFields["ID"]);
					if(!empty($skuProductInfo)){
						foreach ($skuProductInfo as $itx => $skuProductInfoElement) {
							$productBySku = CIBlockElement::GetByID($skuProductInfoElement["ID"]);
							if(!empty($productBySku)){
								if($arResProductSku = $productBySku->GetNextElement()){
									$arResProductSkuFields = $arResProductSku->GetFields();
									if(!empty($arResProductSkuFields["DETAIL_PICTURE"])){
										$complectProductFields["PICTURE"] = CFile::ResizeImageGet($arResProductSkuFields["DETAIL_PICTURE"], array("width" => 250, "height" => 250), BX_RESIZE_IMAGE_PROPORTIONAL, false);
									}
								}
							}
						}
					}
				}

				// set empty picture
				if(empty($complectProductFields["PICTURE"]["src"])){
					$complectProductFields["PICTURE"]["src"] = SITE_TEMPLATE_PATH."/images/empty.png";
				}

				$arResult["COMPLECT"]["ITEMS"][$complectProductFields["ID"]] = $complectProductFields;

			}

			$arResult["COMPLECT"]["RESULT_PRICE_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_PRICE"], $OPTION_CURRENCY);
			$arResult["COMPLECT"]["RESULT_BASE_DIFF_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_BASE_DIFF"], $OPTION_CURRENCY);
			$arResult["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"] = CurrencyFormat($arResult["COMPLECT"]["RESULT_BASE_PRICE"], $OPTION_CURRENCY);

			//set price
			$arResult["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"] = $arResult["COMPLECT"]["RESULT_PRICE_FORMATED"];
			$arResult["MIN_PRICE"]["PRINT_VALUE"] = $arResult["COMPLECT"]["RESULT_BASE_PRICE_FORMATED"];
			$arResult["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] = $arResult["COMPLECT"]["RESULT_BASE_DIFF"];

		}

		//price count
		$dbPrice = CPrice::GetList(
	        array(),
	        array("PRODUCT_ID" => $arResult["ID"], "CAN_ACCESS" => "Y"),
	        false,
	        false,
	        array("ID")
	    );
		$arResult["COUNT_PRICES"] = $dbPrice->SelectedRowsCount();

		if(empty($arResult["COMPLECT"]["ITEMS"])){ //bitrix complect bag
			//Информация о складах
			$rsStore = CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $arResult["ID"]), false, false, array("ID", "AMOUNT"));
			while($arNextStore = $rsStore->GetNext()){
				$arResult["PRODUCT_STORES"][] = $arNextStore;
			}
		}

		//tabs
		$arResult["TABS"]["CATALOG_ELEMENT_BACK"] = array("PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco1.png", "NAME" => GetMessage("CATALOG_ELEMENT_BACK"), "LINK" => $arResult["SECTION"]["SECTION_PAGE_URL"]);
		$arResult["TABS"]["CATALOG_ELEMENT_OVERVIEW"] = array(
			"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco2.png",
			"NAME" => GetMessage("CATALOG_ELEMENT_OVERVIEW"),
			"ACTIVE" => "Y",
			"ID" => "browse"
		);

		if (CModule::IncludeModule("catalog")){
			if(CCatalogProductSet::isProductHaveSet(!empty($arResult["~ID"]) ? $arResult["~ID"] : $arResult["ID"], CCatalogProductSet::TYPE_GROUP)){
				$arResult["TABS"]["CATALOG_ELEMENT_SET"] = array(
					"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco3.png",
					"NAME" => GetMessage("CATALOG_ELEMENT_SET"),
					"ID" => "set"
				);
			}
		}

		if(!empty($arResult["COMPLECT"])){
			$arResult["TABS"]["CATALOG_ELEMENT_COMPLECT"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco3.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_COMPLECT"),
				"ID" => "complect"
			);
		}

		if(!empty($arResult["DETAIL_TEXT"])){
			$arResult["TABS"]["CATALOG_ELEMENT_DESCRIPTION"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco8.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_DESCRIPTION"),
				"ID" => "detailText"
			);
		}

		if(!empty($arResult["DISPLAY_PROPERTIES"])){
			$arResult["TABS"]["CATALOG_ELEMENT_CHARACTERISTICS"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco9.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_CHARACTERISTICS"),
				"ID" => "elementProperties"
			);
		}

		if($arResult["SHOW_RELATED"] == "Y"){
			$arResult["TABS"]["CATALOG_ELEMENT_ACCEESSORIES"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco5.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_ACCEESSORIES"),
				"ID" => "related"
			);
		}

		if(!empty($arResult["REVIEWS"])){
			$arResult["TABS"]["CATALOG_ELEMENT_REVIEW"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco4.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_REVIEW"),
				"ID" => "catalogReviews"
			);
		}

		if($arResult["SHOW_SIMILAR"] == "Y"){
			$arResult["TABS"]["CATALOG_ELEMENT_SIMILAR"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco6.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_SIMILAR"),
				"ID" => "similar"
			);
		}

		if($arResult["SHOW_STORES"] == "Y" && $arParams["HIDE_AVAILABLE_TAB"] != "Y" && empty($arResult["COMPLECT"]["ITEMS"])){
			$arResult["TABS"]["CATALOG_ELEMENT_AVAILABILITY"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco7.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_AVAILABILITY"),
				"ID" => "stores"
			);
		}

		if(!empty($arResult["FILES"])){
			$arResult["TABS"]["CATALOG_ELEMENT_FILES"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco11.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_FILES"),
				"ID" => "files"
			);
		}


		if(!empty($arResult["VIDEO"])){
			$arResult["TABS"]["CATALOG_ELEMENT_VIDEO"] = array(
				"PICTURE" => SITE_TEMPLATE_PATH."/images/elementNavIco10.png",
				"NAME" => GetMessage("CATALOG_ELEMENT_VIDEO"),
				"ID" => "video"
			);
		}

		if(!empty($arResult["CATALOG_MEASURE"])){
			//коэффициент еденица измерения
			$rsMeasure = CCatalogMeasure::getList(
				array(),
				array(
					"ID" => $arResult["CATALOG_MEASURE"]
				),
				false,
				false,
				false
			);

			while($arNextMeasure = $rsMeasure->Fetch()){
				$arResult["MEASURES"][$arNextMeasure["ID"]] = $arNextMeasure;
			}
		}
		$obCache->EndDataCache($arResult);
	}

	$arParams["SHOW_REVIEW_FORM"] = $arResult["SHOW_REVIEW_FORM"];

	//related filter
	global $relatedFilter;
	$relatedFilter = array(
		"ID" => $arResult["PROPERTIES"]["RELATED_PRODUCT"]["VALUE"]
	);
	if(!empty($arResult["SECTION_PATH_LIST"])){
		foreach ($arResult["SECTION_PATH_LIST"] as $ik => $arSectionPath) {
			$APPLICATION->AddChainItem($arSectionPath["NAME"], $arSectionPath["SECTION_PAGE_URL"]);
		}
	}
?>

<?
	$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arResult["NAME"].'" />');
	$APPLICATION->AddHeadString('<meta property="og:description" content="'.$arResult["PREVIEW_TEXT"].'" />');
	$APPLICATION->AddHeadString('<meta property="og:url" content="'.$arResult["DETAIL_PAGE_URL"].'" />');
	$APPLICATION->AddHeadString('<meta property="og:type" content="website" />');
	if(!empty($arResult["IMAGES"][0]["LARGE_IMAGE"]["SRC"])){
		$APPLICATION->AddHeadString('<meta property="og:image" content="'.$arResult["IMAGES"][0]["LARGE_IMAGE"]["SRC"].'" />');
	}
?>