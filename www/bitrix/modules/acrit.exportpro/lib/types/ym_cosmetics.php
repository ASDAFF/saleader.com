<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["ym_cosmetics"] = array(
    "CODE" => "ym_cosmetics",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
    "REG" => "http://market.yandex.ru/",
    "HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
    "FIELDS" => array(
        array(
            "CODE" => "ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_ID" ),
            "VALUE" => "ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "AVAILABLE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_AVAILABLE" ),
            "VALUE" => "",
            "TYPE" => "const",
            "CONDITION" => array(
                "CLASS_ID" => "CondGroup",
                "DATA" => array(
                    "All" => "AND",
                    "True" => "True"
                ),
                "CHILDREN" => array(
                    array(
                        "CLASS_ID" => "CondCatQuantity",
                        "DATA" => array(
                                "logic" => "EqGr",
                                "value" => "1"
                        )
                    )
                )
            ),
            "USE_CONDITION" => "Y",
            "CONTVALUE_TRUE" => "true",
            "CONTVALUE_FALSE" => "false",
        ),
        array(
            "CODE" => "BID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_BID" ),
        ),
        array(
            "CODE" => "GROUP_ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_GROUP_ID" ),
        ),
        array(
            "CODE" => "URL",
            "NAME" => "URL ".GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_URL" ),
            "VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "BASE_DELIVERY_COST",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_BASEDELIVERYCOST" ),
        ),
        array(
            "CODE" => "BASE_DELIVERY_DAYS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_BASEDELIVERYDAYS" ),
        ),
        array(
            "CODE" => "BASE_ORDER_BEFORE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_BASEORDERBEFORE" ),
        ),
        array(
            "CODE" => "PRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PRICE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",

        ),
        array(
            "CODE" => "OLDPRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_OLDPRICE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "CURRENCYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_CURRENCY" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
        ),
        array(
            "CODE" => "CATEGORYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_CATEGORY" ),
            "VALUE" => "IBLOCK_SECTION_ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "GROUPID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_GROUPID" ),
            "TYPE" => "field",
        ),
        array(
            "CODE" => "PICTURE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PICTURE" ),
        ),
        array(
            "CODE" => "STORE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_STORE" ),
        ),
        array(
            "CODE" => "PICKUP",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PICKUP" ),
        ),
        array(
            "CODE" => "DELIVERY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_DELIVERY" ),
        ),
        array(
            "CODE" => "LOCAL_DELIVERY_COST",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_LOCALDELIVERYCOST" ),
        ),
        array(
            "CODE" => "LOCAL_DELIVERY_DAYS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_LOCALDELIVERYDAYS" ),
        ),
        array(
            "CODE" => "LOCAL_ORDER_BEFORE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_LOCALORDERBEFORE" ),
        ),
        array(
            "CODE" => "TYPEPREFIX",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_TYPEPREFIX" ),
        ),
        array(
            "CODE" => "VENDOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_VENDOR" ),
        ),
        array(
            "CODE" => "VENDORCODE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_VENDORCODE" ),
        ),
        array(
            "CODE" => "MODEL",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_MODEL" ),
        ),
        array(
            "CODE" => "DESCRIPTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_DESCRIPTION" ),
        ),
        array(
            "CODE" => "BARCODE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_BARCODE" ),
        ),
        array(
            "CODE" => "PARAM_RANGE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM_RANGE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "PARAM_COLOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM_COLOR" ),
        ),
        array(
            "CODE" => "PARAM_VOLUME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM_VOLUME" ),
        ),
        array(
            "CODE" => "PARAM_GENDER",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM_GENDER" ),
        ),
        array(
            "CODE" => "PARAM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM" ),
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
        array(
            "CODE" => "PARAM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM" ),
        ),
    ),
    "FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="#DATE#">
    <shop>
        <name>#SHOP_NAME#</name>
        <company>#COMPANY_NAME#</company>
        <url>#SITE_URL#</url>
        <currencies>#CURRENCY#</currencies>
        <categories>#CATEGORY#</categories>
        <delivery-options>
            <option cost="#BASE_DELIVERY_COST#" days="#BASE_DELIVERY_DAYS#" order-before="#BASE_ORDER_BEFORE#"/>
        </delivery-options>
        <offers>
            #ITEMS#
        </offers>
    </shop>
</yml_catalog>',

    "DATEFORMAT" => "Y-m-d_H:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;

    $profileTypes["ym_cosmetics"]["FIELDS"][8] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PRICE" ),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );

    $profileTypes["ym_cosmetics"]["FIELDS"][9] = array(
        "CODE" => "OLDPRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_OLDPRICE" ),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["ym_cosmetics"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_COSMETICS_PORTAL_REQUIREMENTS" );
$profileTypes["ym_cosmetics"]["PORTAL_VALIDATOR"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_COSMETICS_PORTAL_VALIDATOR" );
$profileTypes["ym_cosmetics"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_COSMETICS_EXAMPLE" );

$profileTypes["ym_cosmetics"]["CURRENCIES"] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>".PHP_EOL;

$profileTypes["ym_cosmetics"]["SECTIONS"] =
    "<category id='#ID#'>#NAME#</category>".PHP_EOL;

$profileTypes["ym_cosmetics"]["ITEMS_FORMAT"] = "
<offer id=\"#ID#\" type=\"vendor.model\" available=\"#AVAILABLE#\" bid=\"#BID#\" group_id=\"#GROUP_ID#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <oldprice>#OLDPRICE#</oldprice>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <picture>#SITE_URL##PICTURE#</picture>
    <store>#STORE#</store>
    <pickup>#PICKUP#</pickup>
    <delivery>#DELIVERY#</delivery>
    <delivery-options>
        <option cost=\"#LOCAL_DELIVERY_COST#\" days=\"#LOCAL_DELIVERY_DAYS#\" order-before=\"#LOCAL_ORDER_BEFORE#\"/>
    </delivery-options>
    <typePrefix>#TYPEPREFIX#</typePrefix>
    <vendor>#VENDOR#</vendor>
    <vendorCode>#VENDORCODE#</vendorCode>
    <model>#MODEL#</model>
    <description>#DESCRIPTION#</description>
    <barcode>#BARCODE#</barcode>
    <param name=\"".GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM_RANGE_VALUE" )."\">#PARAM_RANGE#</param>
    <param name=\"".GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM_COLOR_VALUE" )."\">#PARAM_COLOR#</param>
    <param name=\"".GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM_VOLUME_VALUE" )."\">#PARAM_VOLUME#</param>
    <param name=\"".GetMessage( "ACRIT_EXPORTPRO_MARKET_COSMETICS_FIELD_PARAM_GENDER_VALUE" )."\">#PARAM_GENDER#</param>
    <param name=\"\">#PARAM#</param>
</offer>
";