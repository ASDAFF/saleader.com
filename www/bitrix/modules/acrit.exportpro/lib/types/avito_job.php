<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["avito_job"] = array(
    "CODE" => "avito_job",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
    "REG" => "http://market.yandex.ru/",
    "HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
    "FIELDS" => array(
        array(
            "CODE" => "Id",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_ID" ),
            "VALUE" => "ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "DateBegin",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_DATEBEGIN" ),
        ),
        array(
            "CODE" => "DateEnd",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_DATEEND" ),
        ),
        array(
            "CODE" => "AdStatus",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_ADSTATUS" ),
        ),
        array(
            "CODE" => "AllowEmail",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_ALLOWEMAIL" ),
        ),
        array(
            "CODE" => "ManagerName",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_MANAGERNAME" ),
        ),
        array(
            "CODE" => "ContactPhone",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_CONTACTPHONE" ),
        ),
        array(
            "CODE" => "Region",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_REGION" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "City",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_CITY" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "Street",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_STREET" ),
        ),
        array(
            "CODE" => "District",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_DISTRICT" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "Subway",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_SUBWAY" ),
        ),
        array(
            "CODE" => "Category",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_CATEGORY" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "Industry",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_INDUSTRY" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "Title",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_TITLE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "JobType",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_JOBTYPE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "Experience",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_EXPERIENCE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "Description",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_DESCRIPTION" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "Salary",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_SALARY" ),
        ),
        array(
            "CODE" => "Image",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_AVITO_JOB_FIELD_IMAGE" ),
        ),
    ),
    "FORMAT" => '<?xml version="1.0"?>
<Ads formatVersion="3" target="Avito.ru">
    #ITEMS#
</Ads>',
    
    "DATEFORMAT" => "Y-m-d",
);

$profileTypes["avito_job"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_AVITO_JOB_PORTAL_REQUIREMENTS" );
$profileTypes["avito_job"]["PORTAL_VALIDATOR"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_AVITO_JOB_PORTAL_VALIDATOR" );
$profileTypes["avito_job"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_AVITO_JOB_EXAMPLE" );

$profileTypes["avito_job"]["CURRENCIES"] = "";

$profileTypes["avito_job"]["SECTIONS"] = "";

$profileTypes["avito_job"]["ITEMS_FORMAT"] = "
<Ad>
    <Id>#Id#</Id>
    <DateBegin>#DateBegin#</DateBegin>
    <DateEnd>#DateEnd#</DateEnd>
    <AdStatus>#AdStatus#</AdStatus>
    <AllowEmail>#AllowEmail#</AllowEmail>
    <ManagerName>#ManagerName#</ManagerName>
    <ContactPhone>#ContactPhone#</ContactPhone>
    <Region>#Region#</Region>
    <City>#City#</City>
    <Street>#Street#</Street>
    <District>#District#</District>
    <Subway>#Subway#</Subway>
    <Category>#Category#</Category>
    <Industry>#Industry#</Industry>
    <Title>#Title#</Title>
    <JobType>#JobType#</JobType>
    <Experience>#Experience#</Experience>
    <Description>#Description#</Description>
    <Salary>#Salary#</Salary>
    <Images>
        <Image url=\"#SITE_URL##Image#\"></Image>
    </Images>
</Ad>
";