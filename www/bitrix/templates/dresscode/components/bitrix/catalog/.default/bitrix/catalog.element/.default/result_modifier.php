<?
global $USER;

$OPTION_ADD_CART = COption::GetOptionString("catalog", "default_can_buy_zero");
$OPTION_PRICE_TAB = COption::GetOptionString("catalog", "show_catalog_tab_with_offers");
$OPTION_CURRENCY = $arResult["CURRENCY"] = CCurrency::GetBaseCurrency();

$arResult["IMAGES"] = array();
$arResult["FILES"] = array();

function picture_separate_array_push($pictureID, $arPushImage = array())
{
    $arPushImage["SMALL_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER);
    $arPushImage["MEDIUM_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => 500, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER);
    $arPushImage["LARGE_IMAGE"] = CFile::GetByID($pictureID)->Fetch();
    $arPushImage["LARGE_IMAGE"]["SRC"] = CFile::GetPath($arPushImage["LARGE_IMAGE"]["ID"]);
    return $arPushImage;
}

if (!empty($arResult["PROPERTIES"]["ATT_BRAND"]["VALUE"])) {
    $arFilter = Array("ID" => $arResult["PROPERTIES"]["ATT_BRAND"]["VALUE"], "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, array("*"));
    if ($res = $res->GetNextElement()) {
        $arResult["BRAND"] = $res->GetFields();
        $arResult["BRAND"]["PICTURE"] = CFile::ResizeImageGet($arResult["BRAND"]["DETAIL_PICTURE"], array("width" => 250, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false);
    }
}

// video and files
foreach ($arResult["PROPERTIES"] as $ips => $arProperty) {
    if ($arProperty["PROPERTY_TYPE"] == "F" && $arProperty["CODE"] != "MORE_PHOTO" && !empty($arProperty["VALUE"])) {
        if (is_array($arProperty["VALUE"])) {
            foreach ($arProperty["VALUE"] as $ipv => $arPropertyValue) {
                $arTmpFile = CFile::GetByID($arPropertyValue)->Fetch();
                $arTmpFile["PARENT_NAME"] = $arProperty["NAME"];
                $arTmpFile["SRC"] = CFile::GetPath($arTmpFile["ID"]);
                $arResult["FILES"][] = $arTmpFile;
            }
        } else {
            $arTmpFile = CFile::GetByID($arProperty["VALUE"])->Fetch();
            $arTmpFile["PARENT_NAME"] = $arProperty["NAME"];
            $arTmpFile["SRC"] = CFile::GetPath($arTmpFile["ID"]);
            $arResult["FILES"][] = $arTmpFile;
        }
    } elseif ($arProperty["CODE"] == "VIDEO" && !empty($arProperty["VALUE"])) {
        if (is_array($arProperty["VALUE"])) {
            foreach ($arProperty["VALUE"] as $ipv => $arPropertyValue) {
                $arResult["VIDEO"][] = $arPropertyValue;
            }
        } else {
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

while ($arPriceType = $dbPriceType->Fetch()) {
    $PRICE_CODES[$arPriceType["NAME"]] = $arPriceType["ID"];
}

$arElement = CIblockElement::GetById($arResult["ID"])->GetNext();
$arResult['DETAIL_PAGE_URL_TMP'] = $arResult['DETAIL_PAGE_URL'];
$arResult['DETAIL_PAGE_URL'] = $arElement['DETAIL_PAGE_URL'];

$db_old_groups = CIBlockElement::GetElementGroups(!empty($arParams["USE_SKU"]) ? $arBaseProduct["ID"] : $arResult["ID"], false);

while ($ar_group = $db_old_groups->Fetch()) {
    $arSection[$ar_group["DEPTH_LEVEL"]] = $ar_group["ID"];
}

!empty($arSection) && krsort($arSection);

if (!empty($arSection)) {
    $arResult["LAST_SECTION"] = array_slice($arSection, 0, 1);
    $res = CIBlockSection::GetByID($arResult["LAST_SECTION"][0]);
    if ($arSec = $res->GetNext()) {
        $arResult["LAST_SECTION"] = $arSec;
    }
}

$nav = CIBlockSection::GetNavChain(false, $arSec["ID"]);
while ($arSectionPath = $nav->GetNext()) {
    $APPLICATION->AddChainItem($arSectionPath["NAME"], $arSectionPath["SECTION_PAGE_URL"]);
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

if (!empty($arResult["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"])) {

    $arSelect = Array("ID");
    if (empty($arResult["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"])) {
        $db_old_groups = CIBlockElement::GetElementGroups($arResult["ID"], true);
        while ($ar_group = $db_old_groups->Fetch()) {
            $arSection[$ar_group["DEPTH_LEVEL"]] = $ar_group["ID"];
        }
        krsort($arSection);
        $similarFilter = Array("IBLOCK_ID" => $arResult["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "SECTION_ID" => array_slice($arSection, 0, 1), "!ID" => $arResult["ID"]);
        $gSimilar = CIBlockElement::GetList(array("RAND" => "ASC"), $similarFilter, false, false, $arSelect);
    } else {
        $similarFilter = Array("IBLOCK_ID" => $arResult["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arResult["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"]);
        $gSimilar = CIBlockElement::GetList(array(), $similarFilter, false, false, $arSelect);
    }

    $arResult["SIMILAR_COUNT"] = $gSimilar->SelectedRowsCount();

} elseif(!empty($arResult["IBLOCK_SECTION_ID"])) {
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

if (is_array($SKU_INFO)) {
    $properties = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "PROPERTY_TYPE" => "L", "IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"]));
    while ($prop_fields = $properties->GetNext()) {
        if ($prop_fields["SORT"] <= 100) {
            $propValues = array();
            $property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC", "DEF" => "DESC"), Array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "CODE" => $prop_fields["CODE"]));
            while ($enum_fields = $property_enums->GetNext()) {
                $propValues[$enum_fields["EXTERNAL_ID"]] = array(
                    "VALUE" => $enum_fields["VALUE"],
                    "SELECTED" => N,
                    "DISABLED" => N,
                );
            }
            $arResult["PROPERTIES_LIST"][$prop_fields["CODE"]] = array_merge(
                $prop_fields, array("VALUES" => $propValues)
            );
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
if ($arResult["SKU"]) {
    if (empty($SKU_INFO)) {
        $SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arResult["IBLOCK_ID"]);
    }
    if (is_array($SKU_INFO)) {
        $rsOffers = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_" . $SKU_INFO["SKU_PROPERTY_ID"] => $arResult["ID"], "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY"));
        while ($arSku = $rsOffers->GetNextElement()) {

            $arSkuFields = $arSku->GetFields();
            $arSkuProperties = $arSku->GetProperties();

            $arSkuFields["PRICE"] = CCatalogProduct::GetOptimalPrice($arSkuFields["ID"], 1, $USER->GetUserGroupArray());

            if (!empty($arSkuFields["PRICE"])) {
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

if (!empty($arResult["SKU_PRODUCT"]) && !empty($arResult["PROPERTIES_LIST"])) {
    $arResult["SKU_PROPERTIES"] = $arResult["PROPERTIES_LIST"];
    foreach ($arResult["SKU_PROPERTIES"] as $ip => $arProp) {
        foreach ($arProp["VALUES"] as $ipv => $arPropValue) {
            $find = false;;
            foreach ($arResult["SKU_PRODUCT"] as $ipo => $arOffer) {
                if ($arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"] == $arPropValue["VALUE"]) {
                    $find = true;
                    break(1);
                }
            }
            if (!$find) {
                unset($arResult["SKU_PROPERTIES"][$ip]["VALUES"][$ipv]);
            }
        }
    }

    // first display

    $arPropClean = array();
    $iter = 0;

    foreach ($arResult["SKU_PROPERTIES"] as $ip => $arProp) {
        if (!empty($arProp["VALUES"])) {
            $arKeys = array_keys($arProp["VALUES"]);
            $selectedUse = false;
            if ($iter === 0) {
                $arResult["SKU_PROPERTIES"][$ip]["VALUES"][$arKeys[0]]["SELECTED"] = Y;
                $arPropClean[$ip] = array(
                    "PROPERTY" => $ip,
                    "VALUE" => $arKeys[0]
                );
            } else {
                foreach ($arKeys as $key => $keyValue) {
                    $disabled = true;
                    $checkValue = $arResult["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["VALUE"];
                    foreach ($arResult["SKU_PRODUCT"] as $io => $arOffer) {
                        if ($arOffer["PROPERTIES"][$ip]["VALUE"] == $checkValue) {
                            $disabled = false;
                            $selected = true;
                            foreach ($arPropClean as $ic => $arNextClean) {
                                if ($arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE_XML_ID"] != $arNextClean["VALUE"]) {
                                    if ($ic == $ip) {
                                        break(2);
                                    }
                                    $disabled = true;
                                    $selected = false;
                                    break(1);
                                }
                            }
                            if ($selected && !$selectedUse) {
                                $selectedUse = true;
                                $arResult["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["SELECTED"] = Y;
                                $arPropClean[$ip] = array(
                                    "PROPERTY" => $ip,
                                    "VALUE" => $keyValue
                                );
                                break(1);
                            }
                        }
                    }
                    if ($disabled) {
                        $arResult["SKU_PROPERTIES"][$ip]["VALUES"][$keyValue]["DISABLED"] = "Y";
                    }
                }
            }
            $iter++;
        }
    }

    if (!empty($arResult["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE])) {
        foreach ($arResult["SKU_PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUES"] as $ic => $arProperty) {
            foreach ($arResult["SKU_PRODUCT"] as $io => $arOffer) {
                if ($arOffer["PROPERTIES"][$COLOR_PROPERTY_NANE]["VALUE"] == $arProperty["VALUE"]) {
                    if (!empty($arOffer["DETAIL_PICTURE"])) {
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
            if ($arOffer["PROPERTIES"][$arNextClean["PROPERTY"]]["VALUE_XML_ID"] != $arNextClean["VALUE"]) {
                $active = false;
                break(1);
            }
        }

        if ($active) {

            if (!empty($arOffer["DETAIL_PICTURE"])) {
                array_push($arResult["IMAGES"], picture_separate_array_push($arOffer["DETAIL_PICTURE"]));
            }

            if (!empty($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"])) {
                foreach ($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
                    array_push($arResult["IMAGES"], picture_separate_array_push($nextPictureID));
                }
            }

            if (!empty($arOffer["NAME"])) {
                $arResult["NAME"] = $arOffer["NAME"];
            }

            $arResult["~ID"] = $arResult["ID"];

            $arResult["ID"] = $arOffer["ID"];
            $arResult["TMP_PRICE"] = CCatalogProduct::GetOptimalPrice($arOffer["ID"], 1, $USER->GetUserGroupArray());

            $arResult["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"] = CurrencyFormat($arResult["TMP_PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
            $arResult["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] = $arResult["TMP_PRICE"]["RESULT_PRICE"]["DISCOUNT"];
            $arResult["MIN_PRICE"]["PRINT_VALUE"] = CurrencyFormat($arResult["TMP_PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY);

            $arResult["IBLOCK_ID"] = $arOffer["IBLOCK_ID"];
            $arResult["CATALOG_QUANTITY"] = $arOffer["CATALOG_QUANTITY"];
            $arResult["CAN_BUY"] = $OPTION_ADD_CART == "Y" ? true : false;

        }
    }

}

if (empty($arResult["IMAGES"])) {
    if (!empty($arResult["DETAIL_PICTURE"])) {
        array_push($arResult["IMAGES"], picture_separate_array_push($arResult["DETAIL_PICTURE"]["ID"]));
    }

    if (!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])) {
        foreach ($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
            array_push($arResult["IMAGES"], picture_separate_array_push($nextPictureID));
        }
    }
}

$arSelect = Array("ID", "DATE_CREATE", "DETAIL_TEXT", "PROPERTY_DIGNITY", "PROPERTY_SHORTCOMINGS", "PROPERTY_EXPERIENCE", "PROPERTY_GOOD_REVIEW", "PROPERTY_BAD_REVIEW", "PROPERTY_NAME", "PROPERTY_RATING");
$arFilter = Array("IBLOCK_ID" => $arParams["REVIEW_IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "CODE" => !empty($arParams["USE_SKU"]) ? $arBaseProduct["ID"] : $arResult["ID"]);
$res = CIBlockElement::GetList(Array("SORT" => "ASC", "CREATED_DATE"), $arFilter, false, false, $arSelect);
while ($ob = $res->GetNextElement()) {
    $arResult["REVIEWS"][] = $ob->GetFields();
}

$expEnums = CIBlockPropertyEnum::GetList(Array("DEF" => "DESC", "SORT" => "ASC"), Array("IBLOCK_ID" => $arParams["REVIEW_IBLOCK_ID"], "CODE" => "EXPERIENCE"));
while ($enumValues = $expEnums->GetNext()) {
    $arResult["NEW_REVIEW"]["EXPERIENCE"][] = array(
        "ID" => $enumValues["ID"],
        "VALUE" => $enumValues["VALUE"]
    );
}

$USER_ID = $USER->GetID();
$res = CIBlockElement::GetList(
    Array(),
    Array(
        "ID" => intval(!empty($arParams["USE_SKU"]) ? $arBaseProduct["ID"] : $arResult["ID"]),
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    ),
    false,
    false,
    Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_USER_ID",
    )
);

while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    if ($USER_ID == $arFields["PROPERTY_USER_ID_VALUE"]) {
        $arParams["SHOW_REVIEW_FORM"] = false;
        break;
    }
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

//tabs
$arResult["TABS"]["CATALOG_ELEMENT_BACK"] = array("PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco1.png", "NAME" => GetMessage("CATALOG_ELEMENT_BACK"), "LINK" => $arResult["SECTION"]["SECTION_PAGE_URL"]);
$arResult["TABS"]["CATALOG_ELEMENT_OVERVIEW"] = array(
    "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco2.png",
    "NAME" => GetMessage("CATALOG_ELEMENT_OVERVIEW"),
    "ACTIVE" => "Y",
    "ID" => "browse"
);

if (CModule::IncludeModule("catalog")) {
    if (CCatalogProductSet::isProductHaveSet(!empty($arResult["~ID"]) ? $arResult["~ID"] : $arResult["ID"], CCatalogProductSet::TYPE_GROUP)) {
        $arResult["TABS"]["CATALOG_ELEMENT_SET"] = array(
            "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco3.png",
            "NAME" => GetMessage("CATALOG_ELEMENT_SET"),
            "ID" => "set"
        );
    }
}

if (!empty($arResult["DETAIL_TEXT"])) {
    $arResult["TABS"]["CATALOG_ELEMENT_DESCRIPTION"] = array(
        "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco8.png",
        "NAME" => GetMessage("CATALOG_ELEMENT_DESCRIPTION"),
        "ID" => "detailText"
    );
}

if (!empty($arResult["DISPLAY_PROPERTIES"])) {
    $arResult["TABS"]["CATALOG_ELEMENT_CHARACTERISTICS"] = array(
        "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco9.png",
        "NAME" => GetMessage("CATALOG_ELEMENT_CHARACTERISTICS"),
        "ID" => "elementProperties"
    );
}

if ($arResult["SHOW_RELATED"] == "Y") {
    $arResult["TABS"]["CATALOG_ELEMENT_ACCEESSORIES"] = array(
        "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco5.png",
        "NAME" => GetMessage("CATALOG_ELEMENT_ACCEESSORIES"),
        "ID" => "related"
    );
}

if (!empty($arResult["REVIEWS"])) {
    $arResult["TABS"]["CATALOG_ELEMENT_REVIEW"] = array(
        "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco4.png",
        "NAME" => GetMessage("CATALOG_ELEMENT_REVIEW"),
        "ID" => "catalogReviews"
    );
}

if ($arResult["SHOW_SIMILAR"] == "Y") {
    $arResult["TABS"]["CATALOG_ELEMENT_SIMILAR"] = array(
        "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco6.png",
        "NAME" => GetMessage("CATALOG_ELEMENT_SIMILAR"),
        "ID" => "similar"
    );
}

if ($arResult["SHOW_STORES"] == "Y") {
    $arResult["TABS"]["CATALOG_ELEMENT_AVAILABILITY"] = array(
        "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco7.png",
        "NAME" => GetMessage("CATALOG_ELEMENT_AVAILABILITY"),
        "ID" => "stores"
    );
}

if (!empty($arResult["FILES"])) {
    $arResult["TABS"]["CATALOG_ELEMENT_FILES"] = array(
        "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco11.png",
        "NAME" => GetMessage("CATALOG_ELEMENT_FILES"),
        "ID" => "files"
    );
}


if (!empty($arResult["VIDEO"])) {
    $arResult["TABS"]["CATALOG_ELEMENT_VIDEO"] = array(
        "PICTURE" => SITE_TEMPLATE_PATH . "/images/elementNavIco10.png",
        "NAME" => GetMessage("CATALOG_ELEMENT_VIDEO"),
        "ID" => "video"
    );
}


?>