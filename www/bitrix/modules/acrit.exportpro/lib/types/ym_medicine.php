<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["ym_medicine"] = array(
    "CODE" => "ym_medicine",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
    "REG" => "http://market.yandex.ru/",
    "HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
    "FIELDS" => array(
        array(
            "CODE" => "ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_ID" ),
            "VALUE" => "ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "AVAILABLE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_AVAILABLE" ),
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
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_BID" ),
            "VALUE" => "",
        ),
        array(
            "CODE" => "CBID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_CBID" ),
            "VALUE" => "",
        ),
        array(
            "CODE" => "FEE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_FEE" ),
            "VALUE" => "",
        ),
        array(
            "CODE" => "URL",
            "NAME" => "URL ".GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_URL" ),
            "VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "PRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PRICE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",

        ),
        array(
            "CODE" => "OLDPRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_OLDPRICE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "CURRENCYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_CURRENCY" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
        ),
        array(
            "CODE" => "CATEGORYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_CATEGORY" ),
            "VALUE" => "IBLOCK_SECTION_ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "PICTURE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PICTURE" ),
        ),
        array(
            "CODE" => "STORE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_STORE" ),
        ),
        array(
            "CODE" => "PICKUP",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PICKUP" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "true",
        ),
        array(
            "CODE" => "DELIVERY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_DELIVERY" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "false",
        ),
        array(
            "CODE" => "VENDOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_VENDOR" ),
        ),
        array(
            "CODE" => "VENDORCODE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_VENDORCODE" ),
        ),
        array(
            "CODE" => "NAME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_NAME" ),
            "VALUE" => "NAME",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "DESCRIPTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_DESCRIPTION" ),
        ),
        array(
            "CODE" => "SALES_NOTES",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_SALESNOTES" ),
        ),
        array(
            "CODE" => "COUNTRY_OF_ORIGIN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_COUNTRYOFORIGIN" ),
        ),
        array(
            "CODE" => "BARCODE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_BARCODE" ),
        ),
        array(
            "CODE" => "CPA",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_CPA" ),
        ),
        array(
            "CODE" => "EXPIRY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_EXPIRY" ),
        ),
        array(
            "CODE" => "WEIGHT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_WEIGHT" ),
        ),
        array(
            "CODE" => "DIMENSIONS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_DIMENSIONS" ),
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
        array(
            "CODE" => "PARAM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PARAM" ),
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

    $profileTypes["ym_medicine"]["FIELDS"][6] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_PRICE" ),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );

    $profileTypes["ym_medicine"]["FIELDS"][7] = array(
        "CODE" => "OLDPRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_MEDICINE_FIELD_OLDPRICE" ),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["ym_medicine"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_MEDICINE_PORTAL_REQUIREMENTS" );
$profileTypes["ym_medicine"]["PORTAL_VALIDATOR"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_MEDICINE_PORTAL_VALIDATOR" );
$profileTypes["ym_medicine"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_MEDICINE_EXAMPLE" );

$profileTypes["ym_medicine"]["CURRENCIES"] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>".PHP_EOL;

$profileTypes["ym_medicine"]["SECTIONS"] =
    "<category id='#ID#'>#NAME#</category>".PHP_EOL;

$profileTypes["ym_medicine"]["ITEMS_FORMAT"] = "
<offer id=\"#ID#\" type=\"medicine\" available=\"#AVAILABLE#\" bid=\"#BID#\" cbid=\"#CBID#\" fee=\"#FEE#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <oldprice>#OLDPRICE#</oldprice>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <picture>#SITE_URL##PICTURE#</picture>
    <store>#STORE#</store>
    <pickup>#PICKUP#</pickup>
    <delivery>#DELIVERY#</delivery>
    <vendor>#VENDOR#</vendor>
    <vendorCode>#VENDORCODE#</vendorCode>
    <name>#NAME#</name>
    <description>#DESCRIPTION#</description>
    <sales_notes>#SALES_NOTES#</sales_notes>
    <country_of_origin>#COUNTRY_OF_ORIGIN#</country_of_origin>
    <barcode>#BARCODE#</barcode>
    <cpa>#CPA#</cpa>
    <expiry>#EXPIRY#</expiry>
    <weight>#WEIGHT#</weight>
    <dimensions>#DIMENSIONS#</dimensions>
    <param name=\"\">#PARAM#</param>
</offer>
";