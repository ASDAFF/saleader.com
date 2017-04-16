<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["ym_event_ticket"] = array(
	"CODE" => "ym_event_ticket",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_NAME" ),
	"DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_ID" ),
            "VALUE" => "ID",
			"REQUIRED" => "Y",
            "TYPE" => "field",
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_AVAILABLE" ),
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
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_BID" ),
            "VALUE" => "",
        ),
        array(
            "CODE" => "CBID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_CBID" ),
            "VALUE" => "",
        ),
        array(
            "CODE" => "FEE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_FEE" ),
            "VALUE" => "",
        ),
		array(
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_URL" ),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
		),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PRICE" ),
			"REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
            "CODE" => "OLDPRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_OLDPRICE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_CURRENCY" ),
			"REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_CATEGORY" ),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => "Y",
            "TYPE" => "field",
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PICTURE" ),
		),
        array(
			"CODE" => "DELIVERY",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_DELIVERY" ),
		),
        array(
            "CODE" => "LOCAL_DELIVERY_COST",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_LOCALDELIVERYCOST" ),
        ),
        array(
            "CODE" => "LOCAL_DELIVERY_DAYS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_LOCALDELIVERYDAYS" ),
        ),
        array(
            "CODE" => "LOCAL_ORDER_BEFORE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_LOCALORDERBEFORE" ),
        ),
        array(
			"CODE" => "NAME",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_NAME" ),
            "REQUIRED" => "Y",
            "VALUE" => "NAME",
            "TYPE" => "field",
		),
        array(
            "CODE" => "DESCRIPTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_DESCRIPTION" ),
        ),
        array(
            "CODE" => "AGE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_AGE" ),
        ),
        array(
            "CODE" => "STORE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_STORE" ),
        ),
        array(
            "CODE" => "PICKUP",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PICKUP" ),
        ),
		array(
            "CODE" => "SALES_NOTES",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_SALESNOTES" ),
        ),
        array(
            "CODE" => "ADULT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_ADULT" ),
        ),
        array(
            "CODE" => "BARCODE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_BARCODE" ),
        ),
        array(
            "CODE" => "PLACE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PLACE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "HALL",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_HALL" ),
        ),
        array(
            "CODE" => "HALL_PART",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_HALLPART" ),
        ),
        array(
            "CODE" => "DATE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_DATE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "IS_PREMIERE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_ISPREMIERE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "IS_KIDS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_ISKIDS" ),
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
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

	"DATEFORMAT" => "Y-m-d_h:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;

    $profileTypes["ym_event_ticket"]["FIELDS"][6] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_PRICE" ),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );


    $profileTypes["ym_event_ticket"]["FIELDS"][7] = array(
        "CODE" => "OLDPRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_EVENTTICKET_FIELD_OLDPRICE" ),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["ym_event_ticket"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_EVENTTICKET_PORTAL_REQUIREMENTS" );
$profileTypes["ym_event_ticket"]["PORTAL_VALIDATOR"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_EVENTTICKET_PORTAL_VALIDATOR" );
$profileTypes["ym_event_ticket"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_MARKET_EVENTTICKET_EXAMPLE" );

$profileTypes["ym_event_ticket"]["CURRENCIES"] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>".PHP_EOL;

$profileTypes["ym_event_ticket"]["SECTIONS"] =
    "<category id='#ID#'>#NAME#</category>".PHP_EOL;

$profileTypes["ym_event_ticket"]["ITEMS_FORMAT"] = "
<offer id=\"#ID#\" type=\"event-ticket\" available=\"#AVAILABLE#\" bid=\"#BID#\" cbid=\"#CBID#\" fee=\"#FEE#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <oldprice>#OLDPRICE#</oldprice>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <picture>#SITE_URL##PICTURE#</picture>
    <delivery>#DELIVERY#</delivery>
    <delivery-options>
        <option cost=\"#LOCAL_DELIVERY_COST#\" days=\"#LOCAL_DELIVERY_DAYS#\" order-before=\"#LOCAL_ORDER_BEFORE#\"/>
    </delivery-options>
    <name>#NAME#</name>
    <description>#DESCRIPTION#</description>
    <age>#AGE#</age>
    <store>#STORE#</store>
    <pickup>#PICKUP#</pickup>
    <sales_notes>#SALES_NOTES#</sales_notes>
    <adult>#ADULT#</adult>
    <barcode>#BARCODE#</barcode>
    <place>#PLACE#</place>
    <hall>#HALL#</hall>
    <hall_part>#HALL_PART#</hall_part>
    <date>#DATE#</date>
    <is_premiere>#IS_PREMIERE#</is_premiere>
    <is_kids>#IS_KIDS#</is_kids>
</offer>
";