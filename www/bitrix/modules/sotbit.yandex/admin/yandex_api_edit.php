<?
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\ExpressionField;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin.php');
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$module_id = "sotbit.yandex";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");

//use Bitrix\Main\Localization\Loc as Loc;
//Loc::loadMessages(__FILE__);

CJSCore::Init(array("ajax", "jquery"));
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION, $DB;

$ID = intval($ID);        // Id of the edited record
$bCopy = ($action == "copy");
$message = null;
$bVarsFromForm = false;

if($_REQUEST["TYPE"]=="getCat" || $_REQUEST["TYPE"]=="loadCat" || $_REQUEST["TYPE"]=="setCat" || $_REQUEST["TYPE"]=="fileProp" || $_REQUEST["TYPE"]=="stringProp" || $_REQUEST["TYPE"]=="start" || $_REQUEST["TYPE"]=="current")
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sotbit.yandex/classes/ajax.php");

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT <= "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}


$KEY_API = COption::GetOptionString("sotbit.yandex", "KEY", "");


$yandex_table = "b_sotbit_yandex_list";
$arRequired = array("NAME", "IBLOCK_ID", "SETTINGS[GEO][REGION_NAME]");
//$ID = null;
$arData = array();
if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid())
{
    if($visual_REGION_NAME_ID)
    {
        $SETTINGS["GEO"]["REGION_NAME"] = $visual_REGION_NAME_ID;
    }
    if(isset($YA_CAT["VALUES"]))
    {
        foreach($YA_CAT["VALUES"] as $i=>$val)
        {
            if(in_array($val, $SETTINGS["CAT"]["VALUES"]))
                $YA_CAT["VALUES"][$i] = $val."|selected";
        }
        $SETTINGS["CAT"]["VALUES"] = $YA_CAT["VALUES"];
        unset($YA_CAT["VALUES"]);
    }

    CSotbitYandex::setSettings($SETTINGS);

    $arFields = Array(
        "NAME"    => $NAME,
        "ACTIVE"    => ($ACTIVE <> "Y"? "N":"Y"),
        "MODE" => $MODE,
        "TASK" => $TASK,
        "IBLOCK_ID"    => $IBLOCK_ID,
        "AGENT" => ($AGENT <> "Y"? "N":"Y"),
        "AGENT_TIME" => $AGENT_TIME,
        "SETTINGS" => base64_encode(serialize($SETTINGS))
    );

    if($ID>0)
    {
        $result = SotbitYandexTable::Update($ID, $arFields);

        if (!$result->isSuccess())
        {
            $errors = $result->getErrorMessages();
            $res = false;
        }else
            $res = true;
    }
    else
    {
        $result = SotbitYandexTable::add($arFields);

        if ($result->isSuccess())
        {
            $ID = $result->getId();
            $res = ($ID>0);
        }else{
            $errors = $result->getErrorMessages();
            $res = false;
        }
        
    }
    
    if($ID>0 && $AGENT_TIME>0){
        $arAgent = CAgent::GetList(array(), array("NAME"=>"CSotbitYandex::startAgent(".$ID.");"))->Fetch();
        if(!$arAgent && $AGENT=="Y"){CAgent::AddAgent(
            "CSotbitYandex::startAgent(".$ID.");", // имя функции
            "sotbit.yandex",                          // идентификатор модуля
            "N",                                  // агент не критичен к кол-ву запусков
            $AGENT_TIME,                                // интервал запуска - 1 сутки
            "",                // дата первой проверки на запуск
            "Y",                                  // агент активен
            "",                // дата первого запуска
            100
          );}
        elseif($arAgent){
            CAgent::Update($arAgent['ID'], array(
                "AGENT_INTERVAL"=>$AGENT_TIME,
                "ACTIVE"=>$AGENT=="Y"?"Y":"N"
            ));
        }
    }
    
    if($res)
    {   
        if($apply!="")
            LocalRedirect("/bitrix/admin/sotbit.yandex_api_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&tabControl_active_tab=".$_REQUEST["tabControl_active_tab"]);
        else
            LocalRedirect("/bitrix/admin/sotbit.yandex_api_list.php?lang=".LANG);
    }
    else
    {
        if(isset($errors) && !empty($errors))
        {
            foreach($errors as $error)
            {
                CSotbitYandex::MessageError($error);
            }

        }
        $bVarsFromForm = true;
    }
}
ClearVars(); 


if(isset($_REQUEST["ID"]) || $copy)
{
    $ID = (int)$_REQUEST["ID"];
    if($copy)
        $arDataTable = SotbitYandexTable::GetByID($copy)->Fetch();
    else
        $arDataTable = SotbitYandexTable::GetByID($ID)->Fetch();    
    
    if($arDataTable["SETTINGS"])
    {
        $arDataTable["SETTINGS"] = (string)$arDataTable["SETTINGS"];
        $arDataTable["SETTINGS"] = unserialize(base64_decode($arDataTable["SETTINGS"]));
    }
    
    $geoName = $arDataTable["SETTINGS"]["GEO"]["REGION_NAME"];
    preg_match("/\[(\\d+)\]/", $geoName, $match);
    $geoID = $match[1];
    $IBLOCK_ID = $arDataTable["IBLOCK_ID"];
    
    
    if(!empty($IBLOCK_ID)){
        $rsSections = CIBlockSection::GetList(array("left_margin"=>"asc"), array('ACTIVE'=>"Y", "IBLOCK_ID"=>$IBLOCK_ID), false, array('ID', 'NAME', "IBLOCK_ID", "DEPTH_LEVEL"));

        while($arr=$rsSections->Fetch()){
            $arr["NAME"] = str_repeat(" . ", $arr["DEPTH_LEVEL"]).$arr["NAME"];
            $arSection[] = $arr;
        }

    }
 
} 
if(!isset($geoID))
    $geoID = 213;
//if($bVarsFromForm)
    //$DB->InitTableVarsForEdit("b_sotbit_yandex_list", "", "s_ya_");

$APPLICATION->SetTitle(($ID>0? GetMessage("sotbit_yandex_title_edit") : GetMessage("sotbit_yandex_title_add")));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$aMenu = array(
    array(
        "TEXT"=>GetMessage("sotbit_yandex_list"),
        "TITLE"=>GetMessage("sotbit_yandex_list_title"),
        "LINK"=>"sotbit.yandex_api_list.php?lang=".LANG,
        "ICON"=>"btn_list",
    )
);
if($ID>0)
{
    $aMenu[] = array("SEPARATOR"=>"Y");
    $aMenu[] = array(
        "TEXT"=>GetMessage("sotbit_yandex_add"),
        "TITLE"=>GetMessage("sotbit_yandex_add_mnu"),
        "LINK"=>"sotbit.yandex_api_edit.php?lang=".LANG,
        "ICON"=>"btn_new",
    );
    $aMenu[] = array(
        "TEXT"=>GetMessage("sotbit_yandex_copy"),
        "TITLE"=>GetMessage("sotbit_yandex_copy_mnu"),
        "LINK"=>"sotbit.yandex_api_edit.php?copy=".$ID."&lang=".LANG,
        "ICON"=>"btn_copy",
    );
    $aMenu[] = array(
        "TEXT"=>GetMessage("sotbit_yandex_delete"),
        "TITLE"=>GetMessage("sotbit_yandex_delete_mnu"),
        "LINK"=>"javascript:if(confirm('".GetMessage("sotbit_yandex_mnu_del_conf")."'))window.location='sotbit.yandex_api_list.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
        "ICON"=>"btn_delete",
    );

    if($arDataTable["ACTIVE"]=="Y"){
        $aMenu[] = array("SEPARATOR"=>"Y");
        $aMenu[] = array(
            "TEXT"=>GetMessage("sotbit_yandex_start"),
            "TITLE"=>GetMessage("sotbit_yandex_start_mnu"),
            "LINK"=>"sotbit.yandex_api_edit.php?start=1&lang=".LANG."&ID=".$ID,
            "ICON"=>"btn_start"
        );
    }
    $aMenu[] = array(
        "TEXT"=>GetMessage("sotbit_yandex_instructions"),
        "TITLE"=>GetMessage("sotbit_yandex_instructions"),
        "LINK"=>"http://www.sotbit.ru/info/articles/kontentnoe-api-yandeks-market-zagruzka-tovarov-kharakteristik-i-obnovlenie-tsen.html",
        "ICON"=>"instruction"
    );
}
$context = new CAdminContextMenu($aMenu);
$context->Show();


$isOfferCatalog = false;
if(isset($IBLOCK_ID) && $IBLOCK_ID && CModule::IncludeModule('catalog'))
{
    $arIblock = CCatalogSKU::GetInfoByIBlock($IBLOCK_ID);
    if(is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"]!=0 && $arIblock["SKU_PROPERTY_ID"]!=0)$isOfferCatalog = true;
}

if(CModule::IncludeModule('catalog') && (($IBLOCK_ID && CCatalog::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y", "ID"=>$IBLOCK_ID))->Fetch()) || (is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"]!=0 && $arIblock["SKU_PROPERTY_ID"]!=0)  || !$IBLOCK_ID))
{
    $aTabs = array(
        array(
            "DIV" => "edit1",
            "TAB" => GetMessage("sotbit_yandex_task_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_task_tab")
        ),
        array(
            "DIV" => "edit2",
            "TAB" => GetMessage("sotbit_yandex_region_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_region_tab")
        ),
        array(
            "DIV" => "edit3",
            "TAB" => GetMessage("sotbit_yandex_section_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_section_tab")
        ),
        array(
            "DIV" => "edit4",
            "TAB" => GetMessage("sotbit_yandex_filter_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_filter_tab")
        ),
        array(
            "DIV" => "edit5",
            "TAB" => GetMessage("sotbit_yandex_models_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_models_tab")
        ),
        array(
            "DIV" => "edit6",
            "TAB" => GetMessage("sotbit_yandex_offers_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_offers_tab")
        ),
        array(
            "DIV" => "edit7",
            "TAB" => GetMessage("sotbit_yandex_catalog_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_catalog_tab")
        ),
        array(
            "DIV" => "edit8",
            "TAB" => GetMessage("sotbit_yandex_settings_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_settings_tab")
        ),
        array(
            "DIV" => "edit9",
            "TAB" => GetMessage("sotbit_yandex_logs_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_logs_tab")
        ),
    );
    
    $isCatalog = true;    
}else{
    $aTabs = array(
        array(
            "DIV" => "edit1",
            "TAB" => GetMessage("sotbit_yandex_task_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_task_tab")
        ),
        array(
            "DIV" => "edit2",
            "TAB" => GetMessage("sotbit_yandex_region_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_region_tab")
        ),
        array(
            "DIV" => "edit3",
            "TAB" => GetMessage("sotbit_yandex_section_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_section_tab")
        ),
        array(
            "DIV" => "edit4",
            "TAB" => GetMessage("sotbit_yandex_filter_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_filter_tab")
        ),
        array(
            "DIV" => "edit5",
            "TAB" => GetMessage("sotbit_yandex_models_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_models_tab")
        ),
        array(
            "DIV" => "edit6",
            "TAB" => GetMessage("sotbit_yandex_offers_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_offers_tab")
        ),
        
        array(
            "DIV" => "edit8",
            "TAB" => GetMessage("sotbit_yandex_settings_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_settings_tab")
        ),
        array(
            "DIV" => "edit9",
            "TAB" => GetMessage("sotbit_yandex_logs_tab"),
            "ICON" => "sotbit_yandex_icon",
            "TITLE" => GetMessage("sotbit_yandex_logs_tab")
        ),
    );
    $isCatalog = false;    
}


set_time_limit(0);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if(!isset($_REQUEST["TYPE"]))
{
    $sotbitYandex = new CSotbitYandex($KEY_API);

    if(!isset($arDataTable["SETTINGS"]["CAT"]["VALUES"]) || empty($arDataTable["SETTINGS"]["CAT"]["VALUES"]))
    {
        $arData = $sotbitYandex->getListCategory($geoID);
        $arCat = array();
        if($arData->categories->items)
        {
            foreach($arData->categories->items as $arItem)
            {
                $arCat[] = array("NAME"=>$arItem->name, "ID"=>$arItem->id, "DEPTH_LEVEL"=>1);
            }
        }    
    }
    if($arDataTable["TASK"]=="ym" && !isset($_REQUEST["TYPE"]))
    {
        if(!isset($arDataTable["SETTINGS"]["CAT"]["VALUES"]) || empty($arDataTable["SETTINGS"]["CAT"]["VALUES"]) && count($arCat)==1)
        {
            foreach($arCat as $v)
            {
                $arDataFilter = $sotbitYandex->getFilter($geoID, $v);
                $catFilterID = $v;
                break 1;
            }       
        }elseif(!isset($arCat))
        {
            $countS = 0;
            foreach($arDataTable["SETTINGS"]["CAT"]["VALUES"] as $val)
            {
                $arV = explode("|", $val);
                $selected = "";
                if(isset($arV[3]) && !empty($arV[3]))
                {
                    $countS++;
                    $catFilterID = $arV[0];
                }
            }
            if($countS==1)
            {
                $arDataFilter = $sotbitYandex->getFilter($geoID, $catFilterID);
            }    
        }
            
    }    
}




$arMode['reference'] = array('debug', 'work');
$arMode['reference_id'] = array('debug', 'work');

if(!CSotbitYandex::getDemo())
{
    unset($arMode['reference'][1]);
    unset($arMode['reference_id'][1]);
}

$rsIBlock = CIBlock::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()){
    $arIBlock['REFERENCE'][] = "[".$arr["ID"]."] ".$arr["NAME"];
    $arIBlock['REFERENCE_ID'][] = $arr["ID"];
}

$arUniq["REFERENCE_ID"] = array("name", "xml_id");
$arUniq["REFERENCE"] = array(GetMessage("sotbit_yandex_category_uniq_name"), GetMessage("sotbit_yandex_category_uniq_xml_id"));

$arUniqRecord["REFERENCE_ID"] = array("xml_id", "uf");
$arUniqRecord["REFERENCE"] = array(GetMessage("sotbit_yandex_category_uniq_record_xml_id"), GetMessage("sotbit_yandex_category_uniq_record_uf"));




$arUniqProductRecord["REFERENCE_ID"] = array("xml_id", "prop");
$arUniqProductRecord["REFERENCE"] = array(GetMessage("sotbit_yandex_product_uniq_record_xml_id"), GetMessage("sotbit_yandex_product_uniq_record_prop"));

$arUniqProduct["REFERENCE_ID"] = array("xml_id", "name");
$arUniqProduct["REFERENCE"] = array(GetMessage("sotbit_yandex_product_uniq_xml_id"), GetMessage("sotbit_yandex_product_uniq_name"));

$arProductDescription["REFERENCE_ID"] = array("0", "preview", "detail", "pd");
$arProductDescription["REFERENCE"] = array(GetMessage("sotbit_yandex_product_description_0"), GetMessage("sotbit_yandex_product_description_preview"), GetMessage("sotbit_yandex_product_description_detail"), GetMessage("sotbit_yandex_product_description_pd"));

$arProductPreviewPicture["REFERENCE_ID"] = array("0", "1", "detail");
$arProductPreviewPicture["REFERENCE"] = array(GetMessage("sotbit_yandex_product_preview_picture_0"), GetMessage("sotbit_yandex_product_preview_picture_1"), GetMessage("sotbit_yandex_product_preview_picture_detail"));

$arProductDetailPicture["REFERENCE_ID"] = array("0", "1");
$arProductDetailPicture["REFERENCE"] = array(GetMessage("sotbit_yandex_product_detail_picture_0"), GetMessage("sotbit_yandex_product_detail_picture_1"));

$arProductMorePicture["REFERENCE_ID"] = array("0");
$arProductMorePicture["REFERENCE"] = array(GetMessage("sotbit_yandex_product_more_picture_0"));



/*$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID, "PROPERTY_TYPE"=>"F"));
while($arProp = $properties->Fetch())
{
    //echo '<option value="'.$arProp["CODE"].'">'."[".$arProp["CODE"]."] ".$arProp["NAME"].'</option>';
    $arProductMorePicture["REFERENCE_ID"][] = $arProp["CODE"];
    $arProductMorePicture["REFERENCE"][] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
}*/

$arProductUrl["REFERENCE_ID"] = array("0");
$arProductUrl["REFERENCE"] = array(GetMessage("sotbit_yandex_product_url_0"));

$arOfferShop["REFERENCE_ID"] = array("0");
$arOfferShop["REFERENCE"] = array(GetMessage("sotbit_yandex_product_url_0"));

$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
while($arProp = $properties->Fetch())
{
    //echo '<option value="'.$arProp["CODE"].'">'."[".$arProp["CODE"]."] ".$arProp["NAME"].'</option>';
    if($arProp["PROPERTY_TYPE"]=="S")
    {
        $arProductUrl["REFERENCE_ID"][] = $arProp["CODE"];
        $arProductUrl["REFERENCE"][] = "[".$arProp["CODE"]."] ".$arProp["NAME"];    
    }
    if($arProp["PROPERTY_TYPE"]=="F")
    {
        $arProductMorePicture["REFERENCE_ID"][] = $arProp["CODE"];
        $arProductMorePicture["REFERENCE"][] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
    }
    if($arProp["PROPERTY_TYPE"]=="S" || $arProp["PROPERTY_TYPE"]=="L" || $arProp["PROPERTY_TYPE"]=="E")
    {
        $arOfferShop["REFERENCE_ID"][] = $arProp["CODE"];
        $arOfferShop["REFERENCE"][] = "[".$arProp["CODE"]."] ".$arProp["NAME"];    
    }
    
}

$arProductPrice["REFERENCE_ID"] = array("0", "min", "avg", "max");
$arProductPrice["REFERENCE"] = array(GetMessage("sotbit_yandex_product_price_0"), GetMessage("sotbit_yandex_product_price_min"), GetMessage("sotbit_yandex_product_price_avg"), GetMessage("sotbit_yandex_product_price_max"));


$arProductPriceOffer["REFERENCE_ID"] = array("0", "1");
$arProductPriceOffer["REFERENCE"] = array(GetMessage("sotbit_yandex_product_price_0"), GetMessage("sotbit_yandex_product_price_1"));



$arPriceTerms['reference'] = array(GetMessage("sotbit_yandex_price_terms_no"), GetMessage("sotbit_yandex_price_terms_delta"));
$arPriceTerms['reference_id'] = array('', 'delta');

$arPriceUpDown['reference'] = array(GetMessage("sotbit_yandex_price_updown_no"), GetMessage("sotbit_yandex_price_updown_up"), GetMessage("sotbit_yandex_price_updown_down"));
$arPriceUpDown['reference_id'] = array('', 'up', 'down');

$arPriceValue['reference'] = array(GetMessage("sotbit_yandex_price_percent"), GetMessage("sotbit_yandex_price_abs_value"));
$arPriceValue['reference_id'] = array('percent', 'value');


$arProductDetails['reference'] = array(GetMessage("sotbit_yandex_details_no"), GetMessage("sotbit_yandex_details_all"), GetMessage("sotbit_yandex_details_main"));
$arProductDetails['reference_id'] = array('', 'all', 'main');


$arProductDetailsType['reference'] = array(GetMessage("sotbit_yandex_prop_string"), GetMessage("sotbit_yandex_prop_list"));
$arProductDetailsType['reference_id'] = array('S', 'L');

$arUpdate['reference'] = array(GetMessage("sotbit_yandex_update_N"), GetMessage("sotbit_yandex_update_Y"), GetMessage("sotbit_yandex_update_empty"));
$arUpdate['reference_id'] = array('', 'Y', 'empty');


$arTask['reference'] = array(GetMessage("sotbit_yandex_task_sect"), GetMessage("sotbit_yandex_task_ym"), GetMessage("sotbit_yandex_task_search"));
$arTask['reference_id'] = array('sect', 'ym', 'search');

$hideCatalog = false;
if($isCatalog && CModule::IncludeModule('catalog') && CModule::IncludeModule('currency'))
{
    $dbPriceType = CCatalogGroup::GetList(
        array("SORT" => "ASC"),
        array()
    );

    while ($arPriceTypes = $dbPriceType->Fetch())
    {
        $arPriceType["reference"][] = $arPriceTypes["NAME_LANG"];
        $arPriceType["reference_id"][] = $arPriceTypes["ID"];
    }
    $arConvertCurrency["reference"][] = GetMessage("sotbit_yandex_convert_no");
    $arConvertCurrency["reference_id"][] = "";
    $lcur = CCurrency::GetList(($by="name"), ($order1="asc"), LANGUAGE_ID);
    while($lcur_res = $lcur->Fetch())
    {
        $arCurrency["reference"][] = $lcur_res["FULL_NAME"];
        $arCurrency["reference_id"][] = $lcur_res["CURRENCY"];
        $arConvertCurrency["reference"][] = $lcur_res["FULL_NAME"];
        $arConvertCurrency["reference_id"][] = $lcur_res["CURRENCY"];
    }
    $info = CModule::CreateModuleObject('catalog');
    
    if(!CheckVersion("14.0.0", $info->MODULE_VERSION))
    {   
        $dbResultList = CCatalogMeasure::getList(array(), array(), false, false, array("ID", "CODE", "MEASURE_TITLE", "SYMBOL_INTL", "IS_DEFAULT"));
        while($arMeasure = $dbResultList->Fetch())
        {
            $arAllMeasure["reference_id"][] = $arMeasure["ID"];
            $arAllMeasure["reference"][] = $arMeasure["MEASURE_TITLE"];
        }
    }

    $arVATRef = CatalogGetVATArray(array(), true);
}else $hideCatalog = true; 
$demoText = GetMessage("sotbit_yandex_demo_error");
if(!CSotbitYandex::getDemo())
{
    CSotbitYandex::MessageError($demoText);
}
/*if(!$KEY_API)
{
    $message = GetMessage("sotbit_yandex_no_key");
    $message = htmlspecialcharsBack(GetMessage("sotbit_yandex_no_key"));
    CSotbitYandex::MessageError($message);
}*/

?>
<div id="status_bar" style="display:block;overflow:hidden;">
    <div id="progress_bar" style="width: 500px;float:left;" class="adm-progress-bar-outer">
        <div id="progress_bar_inner" style="width: 0px;" class="adm-progress-bar-inner"></div>
        <div id="progress_text" style="width: 500px;height:32px" class="adm-progress-bar-inner-text"><span id="perc_text" style="height:32px!important;display:inline-block;max-width:440px!important;"><?echo GetMessage("sotbit_yandex_loading")?></span> <span id="percent_val" style="display:inline-block;height:32px;vertical-align:top;">0<span>%</div>
    </div>
    <div id="catalog_bar" style="float:left;width:550px;height:35px;line-height:35px;font-weight:bold;margin-left:30px;"></div>
    <div id="catalog_bar_error" style="float:left;width:350px;height:35px;line-height:35px;font-weight:bold;margin-left:30px;color:red;"></div>
    <div id="current_test"></div>
</div>
<div style="clear:both;"></div>
<div id="sotbit_yandex_task_message"></div>
<div id="sotbit_yandex_message"></div>
<?
CSotbitYandex::MessageError(" ", true);
?>
<form method="POST" id="sotbit_yandex_edit" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?echo bitrix_sessid_post();?>
<input type="hidden" name="ID" value="<?=$ID?>">
<?
$tabControl->Begin();

$tabControl->BeginNextTab();
?>
<?if(isset($ID) && $ID):?>
<tr>
    <td><?echo GetMessage("sotbit_yandex_start_last_time")?>:</td>
    <td><?=$arDataTable["START_LAST_TIME_X"]?$arDataTable["START_LAST_TIME_X"]: GetMessage("sotbit_yandex_last_time_no")?></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_end_last_time")?>:</td>
    <td><?=$arDataTable["END_LAST_TIME_X"]?$arDataTable["END_LAST_TIME_X"]: GetMessage("sotbit_yandex_last_time_no")?></td>
</tr>
<?endif;?>
<tr>
    <td><?echo GetMessage("sotbit_yandex_mode")?>:</td>
    <td><?=SelectBoxFromArray('MODE', $arMode, $arDataTable["MODE"]?$arDataTable["MODE"]:"debug", "", "");?></td>
</tr>
<tr>
    <td></td>
    <td>
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_mode_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_act")?>:</td>
    <td width="60%"><input type="checkbox" name="ACTIVE" value="Y"<?if($arDataTable["ACTIVE"] == "Y" || !$ID) echo " checked"?>>
    </td>
</tr>
<tr>
    <td><span class="required">*</span><?echo GetMessage("sotbit_yandex_name")?>:</td>
    <td><input type="text" name="NAME" value="<?echo $arDataTable["NAME"];?>" size="40" maxlength="250"></td>
</tr>
<tr>
    <td><span class="required">*</span><?echo GetMessage("sotbit_yandex_iblock_id")?>:</td>
    <td><?=SelectBoxFromArray('IBLOCK_ID', $arIBlock, $arDataTable["IBLOCK_ID"], GetMessage("sotbit_yandex_iblock_id"), "id='iblock' style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td></td>
    <td>
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_iblock_id_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?echo GetMessage("sotbit_yandex_task_list")?></td>
</tr>
<tr>
    <td><span class="required">*</span><?echo GetMessage("sotbit_yandex_task")?>:</td>
    <td><?=SelectBoxFromArray('TASK', $arTask, $arDataTable["TASK"], "", "id='iblock' style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td></td>
    <td>
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_task_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<?
$taskVal = $arDataTable["TASK"];
$tabControl->BeginNextTab();
?>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_region_name")?>:</td>
    <td width="60%">
        <?/*?><input size="40" maxlength="300" type="text" name="REGION_NAME" value="<?=$shs_PREVIEW_DELETE_ELEMENT?>"><?*/?>
        <?$APPLICATION->IncludeComponent("sotbit:main.lookup.input", "geography", array(
            "CONTROL_ID" => "REGION_NAME_ID",
            "INPUT_NAME_STRING" => "SETTINGS[GEO][REGION_NAME]",
            "MAX_HEIGHT" => "",
            "MIN_HEIGHT" => "",
            "MAX_WIDTH" => "",
            "INPUT_VALUE_STRING" => isset($arDataTable["SETTINGS"]["GEO"]["REGION_NAME"])?$arDataTable["SETTINGS"]["GEO"]["REGION_NAME"]:GetMessage("sotbit_yandex_geo_default"),
            "START_TEXT" => "",
            "MULTIPLE" => "N",
            "TYPE" => "geography"
    ),
    false
);?>
    </td>
</tr>
<tr>
    <td></td>
    <td>
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_region_name_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<?
$tabControl->BeginNextTab();
?>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_category_name")?>:</td>
    <td width="60%">
        <select name="SETTINGS[CAT][VALUES][]" size="14" id="yaCategory" multiple>
        <?
        if(isset($arDataTable["SETTINGS"]["CAT"]["VALUES"]) && !empty($arDataTable["SETTINGS"]["CAT"]["VALUES"]))
        {
            foreach($arDataTable["SETTINGS"]["CAT"]["VALUES"] as $val)
            {
                $arV = explode("|", $val);
                $selected = "";
                if(isset($arV[3]) && !empty($arV[3]))
                    $selected = 'selected="selected"';
                ?>
                <option data-depth="<?=$arV[1]?>" <?=$selected?> value="<?=$arV[0]?>|<?=$arV[1]?>|<?=$arV[2]?>"><?=str_repeat(" . ", $arV[1])?> <?=$arV[2]?></option>
                <?
            }    
        }
        elseif(!empty($arCat))
        {
            foreach($arCat as $cat)
            {
                ?>
                <option data-depth="1" value="<?=$cat["ID"]?>|<?=$cat["DEPTH_LEVEL"]?>|<?=$cat["NAME"]?>"><?=str_repeat(" . ", $cat["DEPTH_LEVEL"])?> <?=$cat["NAME"]?></option>
                <?
            }
        }
        
        ?>    
        </select>
        <input type="submit" value="<?=GetMessage("sotbit_yandex_category_load")?>" name="refresh" id="loadCat">
        <input type="submit" value="<?=GetMessage("sotbit_yandex_category_reload")?>" name="refresh" id="reloadCat">
    </td>
</tr>
<tr>
    <td></td>
    <td>
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_category_name_descr")?>
        <?=EndNote();?>
    </td>
    
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_category_im_name")?>:</td>
    <td width="60%">
        <select name="SETTINGS[CATEGORY][VALUES][]" size="14" id="imCategory" multiple>
            <?if(isset($arSection) && !empty($arSection)):
                foreach($arSection as $arSect):
                $selsected = "";
                if(in_array($arSect["ID"], $arDataTable["SETTINGS"]["CATEGORY"]["VALUES"]))
                    $selsected = 'selected=""';
            ?>
                <option <?=$selsected?> value="<?=$arSect["ID"]?>"><?=$arSect["NAME"]?></option>

            <?
                endforeach;
            endif;?>   
        </select>
    </td>
</tr>
<tr>
    <td></td>
    <td style="width:60%">
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_category_im_name_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_category_uniq")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[CAT][UNIQ]', $arUniq, $arDataTable["SETTINGS"]["CAT"]["UNIQ"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_category_uniq_record")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[CAT][UNIQ_RECORD]', $arUniqRecord, $arDataTable["SETTINGS"]["CAT"]["UNIQ_RECORD"], "", "style='width:262px' ");?>
    </td>
</tr>
<?
$tabControl->BeginNextTab();
?>
<tr>
    <td style="width:100%;text-align:center;" colspan="2" >
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_filter_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<?
    if(isset($arDataFilter) && !empty($arDataFilter->filters)):?>
    <?foreach($arDataFilter->filters as $arFilter):?>
<tr>
    <td width="40%"><?=$arFilter->name?>:</td>
    <td>
    <?if($arFilter->shortname=="price" || $arFilter->type=="NUMERIC"):?>
        <?echo GetMessage("sotbit_yandex_from")?> <input placeholder="<?=$arFilter->minValue?>" type="text" name="SETTINGS[FILT][<?=$catFilterID?>][<?=$arFilter->id?>][min]" value="<?=$arDataTable["SETTINGS"]["FILT"][$catFilterID][$arFilter->id]['min']?>" size="10" maxlength="250"> <?echo GetMessage("sotbit_yandex_to")?> <input placeholder="<?=$arFilter->maxValue?>" type="text" name="SETTINGS[FILT][<?=$catFilterID?>][<?=$arFilter->id?>][max]" value="<?=$arDataTable["SETTINGS"]["FILT"][$catFilterID][$arFilter->id]['max']?>" size="10" maxlength="250"> <?=$arFilter->unit?>
    <?elseif($arFilter->type=="ENUMERATOR"):?>
    <select name="SETTINGS[FILT][<?=$catFilterID?>][<?=$arFilter->id?>][]" size="14" multiple>  <?=$arFilter->unit?>
        <?if($arFilter->options)
          {
              foreach($arFilter->options as $arOpt)
              {
                  $selected = "";
                  if(in_array($arOpt->valueId, $arDataTable["SETTINGS"]["FILT"][$catFilterID][$arFilter->id]))
                    $selected = 'selected=""';
                  ?>
                  <option <?=$selected?> value="<?=$arOpt->valueId?>"><?=$arOpt->valueText?></option>
                  <?
              }
          }  
        
        ?>
    </select>
    <?elseif($arFilter->type=="BOOL"):?>
    <input type="checkbox" name="SETTINGS[FILT][<?=$catFilterID?>][<?=$arFilter->id?>]" value="Y"<?if($arDataTable["SETTINGS"]["FILT"][$catFilterID][$arFilter->id] == "Y" || !$ID) echo " checked"?>>
    <?endif?>
    
    </td>
</tr>    
    <?endforeach?>
    <?endif;?>
<?
$tabControl->BeginNextTab();
?>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_load")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[PRODUCT][LOAD]" value="Y"<?if($arDataTable["SETTINGS"]["PRODUCT"]["LOAD"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_uniq")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][UNIQ_PRODUCT]', $arUniqProduct, $arDataTable["SETTINGS"]["PRODUCT"]["UNIQ_PRODUCT"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_uniq_record")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][UNIQ_PRODUCT_RECORD]', $arUniqProductRecord, $arDataTable["SETTINGS"]["PRODUCT"]["UNIQ_PRODUCT_RECORD"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_description")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][DESCRIPTION]', $arProductDescription, $arDataTable["SETTINGS"]["PRODUCT"]["DESCRIPTION"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_preview_picture")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][PREVIEW_PICTURE]', $arProductPreviewPicture, $arDataTable["SETTINGS"]["PRODUCT"]["PREVIEW_PICTURE"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_detail_picture")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][DETAIL_PICTURE]', $arProductDetailPicture, $arDataTable["SETTINGS"]["PRODUCT"]["DETAIL_PICTURE"], "", "style='width:262px' ");?>
        <?/*?><?if($disabled):?><input type="hidden" name="IBLOCK_ID" value="<?=$shs_IBLOCK_ID?>" /><?endif;*/?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_more_picture")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][MORE_PICTURE]', $arProductMorePicture, $arDataTable["SETTINGS"]["PRODUCT"]["MORE_PICTURE"], "", "class='fileProp' style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_url")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][URL]', $arProductUrl, $arDataTable["SETTINGS"]["PRODUCT"]["URL"], "", "class='stringProp' style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_price")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][PRICE]', $arProductPrice, $arDataTable["SETTINGS"]["PRODUCT"]["PRICE"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_vendor")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][VENDOR]', $arOfferShop, $arDataTable["SETTINGS"]["PRODUCT"]["VENDOR"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?echo GetMessage("sotbit_yandex_product_details")?></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_detail_load")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][DETAILS]', $arProductDetails, $arDataTable["SETTINGS"]["PRODUCT"]["DETAILS"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_detail_type")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[PRODUCT][PROP_TYPE]', $arProductDetailsType, $arDataTable["SETTINGS"]["PRODUCT"]["PROP_TYPE"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td></td>
    <td style="width:60%">
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_detail_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?echo GetMessage("sotbit_yandex_product_update")?></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_update_check")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[PRODUCT][UPDATE]" value="Y"<?if($arDataTable["SETTINGS"]["PRODUCT"]["UPDATE"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_update_price")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[PRODUCT][UPDATE_PRICE]" value="Y"<?if($arDataTable["SETTINGS"]["PRODUCT"]["UPDATE_PRICE"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_update_param")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[PRODUCT][UPDATE_PARAM]" value="Y"<?if($arDataTable["SETTINGS"]["PRODUCT"]["UPDATE_PARAM"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_product_update_preview_text")?>:</td>
    <td width="60%">
        <?=SelectBoxFromArray('SETTINGS[PRODUCT][UPDATE_PREVIEW_TEXT]', $arUpdate, $arDataTable["SETTINGS"]["PRODUCT"]["UPDATE_PREVIEW_TEXT"], "", "");?>
    </td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_product_update_detail_text")?>:</td>
    <td width="60%">
        <?=SelectBoxFromArray('SETTINGS[PRODUCT][UPDATE_DETAIL_TEXT]', $arUpdate, $arDataTable["SETTINGS"]["PRODUCT"]["UPDATE_DETAIL_TEXT"], "", "");?>
    </td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_product_update_preview_picture")?>:</td>
    <td width="60%">
        <?=SelectBoxFromArray('SETTINGS[PRODUCT][UPDATE_PREVIEW_PICTURE]', $arUpdate, $arDataTable["SETTINGS"]["PRODUCT"]["UPDATE_PREVIEW_PICTURE"], "", "");?>
    </td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_product_update_detail_picture")?>:</td>
    <td width="60%">
        <?=SelectBoxFromArray('SETTINGS[PRODUCT][UPDATE_DETAIL_PICTURE]', $arUpdate, $arDataTable["SETTINGS"]["PRODUCT"]["UPDATE_DETAIL_PICTURE"], "", "");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_update_props")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[PRODUCT][UPDATE_PROPS]" value="Y"<?if($arDataTable["SETTINGS"]["PRODUCT"]["UPDATE_PROPS"] == "Y") echo " checked"?> /></td>
</tr>
<?
$tabControl->BeginNextTab();
?>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_load")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[OFFER][LOAD]" value="Y"<?if($arDataTable["SETTINGS"]["OFFER"]["LOAD"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_uniq")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][UNIQ_PRODUCT]', $arUniqProduct, $arDataTable["SETTINGS"]["OFFER"]["UNIQ_PRODUCT"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_uniq_record")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][UNIQ_PRODUCT_RECORD]', $arUniqProductRecord, $arDataTable["SETTINGS"]["OFFER"]["UNIQ_PRODUCT_RECORD"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_description")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][DESCRIPTION]', $arProductDescription, $arDataTable["SETTINGS"]["OFFER"]["DESCRIPTION"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_preview_picture")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][PREVIEW_PICTURE]', $arProductPreviewPicture, $arDataTable["SETTINGS"]["OFFER"]["PREVIEW_PICTURE"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_detail_picture")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][DETAIL_PICTURE]', $arProductDetailPicture, $arDataTable["SETTINGS"]["OFFER"]["DETAIL_PICTURE"], "", "style='width:262px' ");?>
        <?/*?><?if($disabled):?><input type="hidden" name="IBLOCK_ID" value="<?=$shs_IBLOCK_ID?>" /><?endif;*/?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_more_picture")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][MORE_PICTURE]', $arProductMorePicture, $arDataTable["SETTINGS"]["OFFER"]["MORE_PICTURE"], "", "class='fileProp' style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_url")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][URL]', $arProductUrl, $arDataTable["SETTINGS"]["OFFER"]["URL"], "", "class='stringProp' style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_shop")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][SHOP]', $arOfferShop, $arDataTable["SETTINGS"]["OFFER"]["SHOP"], "", "class='stringProp' style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_price")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][PRICE]', $arProductPriceOffer, $arDataTable["SETTINGS"]["OFFER"]["PRICE"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_vendor")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][VENDOR]', $arOfferShop, $arDataTable["SETTINGS"]["OFFER"]["VENDOR"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?echo GetMessage("sotbit_yandex_product_offer_details")?></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_detail_load")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][DETAILS]', $arProductDetails, $arDataTable["SETTINGS"]["OFFER"]["DETAILS"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_detail_type")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[OFFER][PROP_TYPE]', $arProductDetailsType, $arDataTable["SETTINGS"]["OFFER"]["PROP_TYPE"], "", "style='width:262px' ");?>
    </td>
</tr>
<tr>
    <td></td>
    <td style="width:60%">
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_offer_prop_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?echo GetMessage("sotbit_yandex_offer_update")?></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_update_check")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[OFFER][UPDATE]" value="Y"<?if($arDataTable["SETTINGS"]["OFFER"]["UPDATE"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_update_price")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[OFFER][UPDATE_PRICE]" value="Y"<?if($arDataTable["SETTINGS"]["OFFER"]["UPDATE_PRICE"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_offer_update_param")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[OFFER][UPDATE_PARAM]" value="Y"<?if($arDataTable["SETTINGS"]["OFFER"]["UPDATE_PARAM"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_offer_update_preview_text")?>:</td>
    <td width="60%">
        <?=SelectBoxFromArray('SETTINGS[OFFER][UPDATE_PREVIEW_TEXT]', $arUpdate, $arDataTable["SETTINGS"]["OFFER"]["UPDATE_PREVIEW_TEXT"], "", "");?>
    </td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_product_update_detail_text")?>:</td>
    <td width="60%">
        <?=SelectBoxFromArray('SETTINGS[OFFER][UPDATE_DETAIL_TEXT]', $arUpdate, $arDataTable["SETTINGS"]["OFFER"]["UPDATE_DETAIL_TEXT"], "", "");?>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_product_update_props")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[OFFER][UPDATE_PROPS]" value="Y"<?if($arDataTable["SETTINGS"]["OFFER"]["UPDATE_PROPS"] == "Y") echo " checked"?> /></td>
</tr>
<?
if(!$hideCatalog):
$tabControl->BeginNextTab();
?>
<tr>
    <td class="field-name" width="40%"><?echo GetMessage("sotbit_yandex_price_type")?>:</td>
    <td width="60%"><?=SelectBoxFromArray('SETTINGS[CATALOG][PRICE_TYPE]', $arPriceType, $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TYPE"]?$arDataTable["SETTINGS"]["CATALOG"]["PRICE_TYPE"]:1, "", "");?></td>
</tr>
<tr>
    <td class="field-name" width="40%"><?echo GetMessage("sotbit_yandex_cat_vat_id")?>:</td>
    <td width="60%"><?=SelectBoxFromArray('SETTINGS[CATALOG][CAT_VAT_ID]', $arVATRef, $shs_SETTINGS["CATALOG"]["CAT_VAT_ID"], "", "");?></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_cat_vat_included")?>:</td>
    <td><input class="bool-delete" type="checkbox" name="SETTINGS[CATALOG][CAT_VAT_INCLUDED]" value="Y"<?if($arDataTable["SETTINGS"]["CATALOG"]["CAT_VAT_INCLUDED"] == "Y") echo " checked"?> /></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_currency")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[CATALOG][CURRENCY]', $arCurrency, $arDataTable["SETTINGS"]["CATALOG"]["CURRENCY"]?$arDataTable["SETTINGS"]["CATALOG"]["CURRENCY"]:"RUB", "", "");?></td>
</tr>
<?if(isset($arAllMeasure)):?>
<tr>
    <td><?echo GetMessage("sotbit_yandex_measure")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[CATALOG][MEASURE]', $arAllMeasure, $arDataTable["SETTINGS"]["CATALOG"]["measure"]?$arDataTable["SETTINGS"]["CATALOG"]["MEASURE"]:5, "", "");?></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_catalog_koef")?>:</td>
    <td><input type="text" name="SETTINGS[CATALOG][KOEF]" value="<?echo $arDataTable["SETTINGS"]["CATALOG"]["KOEF"]?$arDataTable["SETTINGS"]["CATALOG"]["KOEF"]:1;?>" size="40" maxlength="250"></td>
</tr>
<tr class="heading">
    <td colspan="2"><?echo GetMessage("sotbit_yandex_work_price")?></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_convert_currency")?>:</td>
    <td><?=SelectBoxFromArray('SETTINGS[CATALOG][CONVERT_CURRENCY]', $arConvertCurrency, $arDataTable["SETTINGS"]["CATALOG"]["CONVERT_CURRENCY"], "", "");?></td>
</tr>
<?if(isset($arDataTable["SETTINGS"]["CATALOG"]["PRICE_UPDOWN"]) && !empty($arDataTable["SETTINGS"]["CATALOG"]["PRICE_UPDOWN"])):?>
    <?foreach($arDataTable["SETTINGS"]["CATALOG"]["PRICE_UPDOWN"] as $i=>$val):
    $class = "usl".$i;
    ?>
    <tr class="heading terms <?=$class?>" data-num="<?=($i)?>">
        <td colspan="2"><?echo GetMessage("sotbit_yandex_work_price_num")?> <span><?=($i+1)?></span> <a href="#" style="font-size:12px;" class="add_usl"><?echo GetMessage("sotbit_yandex_price_num_add")?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" style="font-size:12px;color:red" class="del_usl" data-num="<?=($i)?>"><?echo GetMessage("sotbit_yandex_price_num_del")?></a></td>
    </tr>
    <tr class="<?=$class?>">
        <td><?echo GetMessage("sotbit_yandex_price_updown")?>:</td>
        <td><?=SelectBoxFromArray('SETTINGS[CATALOG][PRICE_UPDOWN][]', $arPriceUpDown, $arDataTable["SETTINGS"]["CATALOG"]["PRICE_UPDOWN"][$i], "", "");?></td>
    </tr>
    <tr class="<?=$class?>">
        <td><?echo GetMessage("sotbit_yandex_price_terms")?>:</td>
        <td><?=SelectBoxFromArray('SETTINGS[CATALOG][PRICE_TERMS][]', $arPriceTerms, $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TERMS"][$i], "", "");?> <?echo GetMessage("sotbit_yandex_price_from")?> <input type="text" name="SETTINGS[CATALOG][PRICE_TERMS_VALUE][]" value="<?echo $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TERMS_VALUE"][$i];?>" size="10" maxlength="250"> <?echo GetMessage("sotbit_yandex_price_to")?> <input type="text" name="SETTINGS[CATALOG][PRICE_TERMS_VALUE_TO][]" value="<?echo $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TERMS_VALUE_TO"][$i];?>" size="10" maxlength="250"></td>
    </tr>
    <tr class="<?=$class?>">
        <td><?echo GetMessage("sotbit_yandex_price_type_value")?>:</td>
        <td><?=SelectBoxFromArray('SETTINGS[CATALOG][PRICE_TYPE_VALUE][]', $arPriceValue, $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TYPE_VALUE"][$i], "", "");?></td>
    </tr>
    <tr class="<?=$class?> tr_last">
        <td><?echo GetMessage("sotbit_yandex_price_value")?>:</td>
        <td><input type="text" name="SETTINGS[CATALOG][PRICE_VALUE][]" value="<?echo $arDataTable["SETTINGS"]["CATALOG"]["PRICE_VALUE"][$i];?>" size="10" maxlength="250"></td>
    </tr>
    <?endforeach?>
<?else:
$i=0;
$class = "usl".$i;
?>
    <tr class="heading terms <?=$class?>" data-num="<?=($i)?>">
        <td colspan="2"><?echo GetMessage("sotbit_yandex_work_price_num")?> <span><?=($i+1)?></span> <a href="#" style="font-size:12px;" class="add_usl"><?echo GetMessage("sotbit_yandex_price_num_add")?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" style="font-size:12px;color:red" class="del_usl" data-num="<?=($i)?>"><?echo GetMessage("sotbit_yandex_price_num_del")?></a></td>
    </tr>
    <tr class="<?=$class?>">
        <td><?echo GetMessage("sotbit_yandex_price_updown")?>:</td>
        <td><?=SelectBoxFromArray('SETTINGS[CATALOG][PRICE_UPDOWN][]', $arPriceUpDown, $arDataTable["SETTINGS"]["CATALOG"]["PRICE_UPDOWN"][$i], "", "");?></td>
    </tr>
    <tr class="<?=$class?>">
        <td><?echo GetMessage("sotbit_yandex_price_terms")?>:</td>
        <td><?=SelectBoxFromArray('SETTINGS[CATALOG][PRICE_TERMS][]', $arPriceTerms, $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TERMS"][$i], "", "");?> <?echo GetMessage("sotbit_yandex_price_from")?> <input type="text" name="SETTINGS[CATALOG][PRICE_TERMS_VALUE][]" value="<?echo $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TERMS_VALUE"][$i];?>" size="10" maxlength="250"> <?echo GetMessage("sotbit_yandex_price_to")?> <input type="text" name="SETTINGS[CATALOG][PRICE_TERMS_VALUE_TO][]" value="<?echo $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TERMS_VALUE_TO"][$i];?>" size="10" maxlength="250"></td>
    </tr>
    <tr class="<?=$class?>">
        <td><?echo GetMessage("sotbit_yandex_price_type_value")?>:</td>
        <td><?=SelectBoxFromArray('SETTINGS[CATALOG][PRICE_TYPE_VALUE][]', $arPriceValue, $arDataTable["SETTINGS"]["CATALOG"]["PRICE_TYPE_VALUE"][$i], "", "");?></td>
    </tr>
    <tr class="<?=$class?> tr_last">
        <td><?echo GetMessage("sotbit_yandex_price_value")?>:</td>
        <td><input type="text" name="SETTINGS[CATALOG][PRICE_VALUE][]" value="<?echo $arDataTable["SETTINGS"]["CATALOG"]["PRICE_VALUE"][$i];?>" size="10" maxlength="250"></td>
    </tr>
<?endif;?>
<?endif;?>
<?
endif;
$tabControl->BeginNextTab();
?>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_active_section")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[DOP][SECT_ACTIVE]" value="Y"<?if($arDataTable["SETTINGS"]["DOP"]["SECT_ACTIVE"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_active_element")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[DOP][ELEMENT_ACTIVE]" value="Y"<?if($arDataTable["SETTINGS"]["DOP"]["ELEMENT_ACTIVE"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_code_section")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[DOP][SECT_CODE]" value="Y"<?if($arDataTable["SETTINGS"]["DOP"]["SECT_CODE"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_code_element")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[DOP][ELEMENT_CODE]" value="Y"<?if($arDataTable["SETTINGS"]["DOP"]["ELEMENT_CODE"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_index_section")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[DOP][SECT_INDEX]" value="Y"<?if($arDataTable["SETTINGS"]["DOP"]["SECT_INDEX"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_index_element")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[DOP][ELEMENT_INDEX]" value="Y"<?if($arDataTable["SETTINGS"]["DOP"]["ELEMENT_INDEX"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_resize_image")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[DOP][RESIZE_IMAGE]" value="Y"<?if($arDataTable["SETTINGS"]["DOP"]["RESIZE_IMAGE"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_step")?>:</td>
    <td><input type="text" name="SETTINGS[DOP][STEP]" value="<?echo isset($arDataTable["SETTINGS"]["DOP"]["STEP"])?$arDataTable["SETTINGS"]["DOP"]["STEP"]:10;?>" size="40" maxlength="250"></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_agent")?>:</td>
    <td width="60%"><input type="checkbox" name="AGENT" value="Y"<?if($arDataTable["AGENT"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td><?echo GetMessage("sotbit_yandex_agent_time")?>:</td>
    <td><input type="text" name="AGENT_TIME" value="<?echo $arDataTable["AGENT_TIME"];?>" size="40" maxlength="250"></td>
</tr>
<tr>
    <td></td>
    <td>
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_agent_descr")?>
        <?=EndNote();?>
    </td>
</tr>
<?
$tabControl->BeginNextTab();
?>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_log_file")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[LOG][FILE]" value="Y"<?if($arDataTable["SETTINGS"]["LOG"]["FILE"] == "Y") echo " checked"?>> <a href="<?=$APPLICATION->GetCurPageParam("log_ID=".$_GET["ID"], array("log_ID"));?>log<?=$_GET["ID"]?>.txt"><?=GetMessage("sotbit_yandex_download_file")?></a></td>
</tr>
<tr>
    <td width="40%"><?echo GetMessage("sotbit_yandex_log_smart")?>:</td>
    <td width="60%"><input type="checkbox" name="SETTINGS[LOG][SMART]" value="Y"<?if($arDataTable["SETTINGS"]["LOG"]["SMART"] == "Y") echo " checked"?>></td>
</tr>
<tr>
    <td></td>
    <td>
        <?=BeginNote();?>
        <?echo GetMessage("sotbit_yandex_log_smart_desc")?>
        <?=EndNote();?>
    </td>
</tr>

<?
if(isset($_GET["log_ID"]) && isset($_GET["ID"])):
    if (ob_get_level()) {
      ob_end_clean();
    }
    $file_log = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/sotbit.yandex/include/log".$_GET["ID"].".txt";
    $file = $file_log;
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit();
endif;

$tabControl->Buttons(
    array(
        "disabled"=>($POST_RIGHT<"W"),
        "back_url"=>"sotbit.yandex_api_list.php?lang=".LANG,

    )
);

$tabControl->End();
?>
<script type="text/javascript">
$(document).ready(function(){
    
    $("#iblock").on("change", function(e){
        show = BX.showWait("#sotbit_yandex_edit");
        BX.ajax.post("/bitrix/admin/sotbit.yandex_api_edit.php?ID=<?=$ID?>", "iblock="+$(this).val()+"&TYPE=fileProp&sessid="+BX.bitrix_sessid(), function(data){
            $(".fileProp").html(data);
            BX.closeWait("#sotbit_yandex_edit", show);    
        })
        show = BX.showWait("#sotbit_yandex_edit");
        BX.ajax.post("/bitrix/admin/sotbit.yandex_api_edit.php?ID=<?=$ID?>", "iblock="+$(this).val()+"&TYPE=stringProp&sessid="+BX.bitrix_sessid(), function(data){
            $(".stringProp").html(data);
            BX.closeWait("#sotbit_yandex_edit", show);    
        })    
    })

    $(document).on("click", "#loadCat", function(e){
        e.preventDefault();
        getOption();
    })
    
    $(document).on("click", "#reloadCat", function(e){
        e.preventDefault();
        reloadOption();
        
    })
    
    function reloadOption()
    {
        $("#yaCategory option").each(function(){
            str = $(this).attr("value");
            if(str.indexOf("|1|")==-1) $(this).remove();
        })
    }
    
    $(document).on("click", ".add_usl", function(e){
        e.preventDefault();
        dataClone = $(".usl0").clone();
        dataClone.removeClass("usl0");
        $(".tr_last").last().after(dataClone);
        len = $(".tr_last").length;
        $(".heading.terms").last().find("span").text(len);
        
    })
    
    $(document).on("click", ".del_usl", function(e){
        e.preventDefault();
        n = parseInt($(this).attr("data-num"));
        $(".usl"+n).remove();
    })
    $("#instruction").attr("target", "_blank");
    /*$("#instruction").click(function(e){
        e.preventDefault();
    })*/
    
    $(document).on("submit", "#sotbit_yandex_edit", function(e){
        if($("#yaCategory option:selected").length>0)
        {
            strV = "";
            $("#yaCategory option").each(function(){
                id = $(this).attr("value");
                strV += '<input type="hidden" name="YA_CAT[VALUES][]" value="'+id+'" />';
            });
            $("#yaCategory").append(strV);
        }
    })
    
    function setOption()
    {
        $("#yaCategory option").each(function(){
            $(this).attr("selected", "true");    
        })    
    }
    var arId = new Array();
    function getOption()
    {
        
        $("#yaCategory option:selected").each(function(){
            id = $(this).attr("value");
            if($.inArray(id, arId)>=0) return true;
            arId.push(id);
            depth = parseInt($(this).attr("data-depth"));
            _this = $(this);
            _next = $(this).next();
            if(_next.length)
            {
                nDepth = parseInt(_next.attr("data-depth"));
                if(nDepth>depth) return true;
            }
            show = BX.showWait("#sotbit_yandex_edit");
            BX.ajax.post("/bitrix/admin/sotbit.yandex_api_edit.php?ID=<?=$ID?>", "catID="+id+"&depth="+(depth+1)+"&TYPE=loadCat&sessid="+BX.bitrix_sessid(), function(data){
                _this.after(data);
                BX.closeWait("#sotbit_yandex_edit", show);
                {
                    getOption();    
                }
                
            })
            
            return false;
        })    
    }
    
    var lastTask = new Array();
    var task = "<?=$taskVal?>";
    var taskStr = "";
    var stop = false;
    
    $(document).on("click", "#btn_stop", function(e){
        e.preventDefault();
        stop = true;
        $(this).text('<?=GetMessage("sotbit_yandex_start")?>');
        $(this).attr("id", "btn_start");
    })
    $(document).on("click", "#btn_start", function(e){
        e.preventDefault();
        if(stop) return false;
        $("#status_bar").show();
        stop = false;
        $(this).text('<?=GetMessage("sotbit_yandex_stop")?>');
        $(this).attr("id", "btn_stop");
        
        var _this = $(this);
        var show;
        show = BX.showWait("#sotbit_yandex_edit");
        BX.ajax.loadJSON("/bitrix/admin/sotbit.yandex_api_edit.php?ID=<?=$ID?>&TYPE=start&task="+task+"&sessid="+BX.bitrix_sessid(), "", function(data){
            if(data)
            {   
                if(data.STATUS=="OK" && data.TASK=="SECT")
                {
                    $("#perc_text").html(data.MESSAGE);
                    $("#percent_val").html(data.PERCENT+"%");
                    $("#catalog_bar").html(data.SUCCESS);
                    objData = new Object();
                    objData.countCat = 0;
                    objData.countCatReal = 0;
                    objData.SECT_ID = 0;
                    objData.DEPTH = 1;
                    loadTask(objData);    
                }
                if(data.STATUS=="OK" && data.TASK=="YM")
                {
                    $("#perc_text").html(data.MESSAGE);
                    $("#percent_val").html(data.PERCENT+"%");
                    $("#catalog_bar").html(data.SUCCESS);
                    objData = new Object();
                    objData.countCat = 0;
                    objData.countProduct = 0;
                    objData.countProductProcent = 0;
                    loadTask(objData);    
                }
                if(data.STATUS=="OK" && data.TASK=="SEARCH")
                {
                    $("#perc_text").html(data.MESSAGE);
                    $("#percent_val").html(data.PERCENT+"%");
                    $("#catalog_bar").html(data.SUCCESS);
                    objData = new Object();
                    objData.countCat = 1;
                    objData.countProduct = 0;
                    objData.countProductProcent = 0;
                    objData.searchCount = 1;
                    objData.searchPage = 1;
                    loadTask(objData);    
                }
                        
            }
        })
        
        
        function loadTask(objData)
        {
            str = "";

            {
                
                $.each(objData, function(index, value) {
                    str+=index+"="+value+"&";
                });
            }
            BX.ajax.loadJSON("/bitrix/admin/sotbit.yandex_api_edit.php?"+str+"ID=<?=$ID?>&TYPE=current&task="+task+"&sessid="+BX.bitrix_sessid(), "", function(data){
                if(data)
                {
                    if(data.STATUS=="OK" && data.TASK=="SECT")
                    {
                        if(data.STEP=="END" || stop)
                        {
                            $("#perc_text").html(data.MESSAGE);
                            $("#percent_val").html(data.PERCENT+"%");
                            $("#catalog_bar").html(data.SUCCESS);
                            if(data.ITOGO_ERROR)
                                $("#catalog_bar_error").html(data.ITOGO_ERROR);
                            prog = parseInt(data.PERCENT);
                            $('#progress_bar_inner').width(500 * prog / 100);
                            _this.text('<?=GetMessage("sotbit_yandex_start")?>');
                            _this.attr("id", "btn_start");
                            if(data.ERROR)
                            {
                                $(".adm-info-message-title").append(data.ERROR);
                                $(".adm-info-message-red").show();
                            }
                            BX.closeWait("#sotbit_yandex_edit", show);
                            stop = false;
                        }else{
                            $("#perc_text").html(data.MESSAGE);
                            $("#percent_val").html(data.PERCENT+"%");
                            $("#catalog_bar").html(data.SUCCESS);
                            if(data.ITOGO_ERROR)
                                $("#catalog_bar_error").html(data.ITOGO_ERROR);
                            prog = parseInt(data.PERCENT);
                            $('#progress_bar_inner').width(500 * prog / 100);
                            objData.countCat = data.countCat;
                            objData.countCatReal = data.countCatReal;
                            objData.SECT_ID = data.SECT_ID;
                            objData.DEPTH = data.DEPTH;
                            objData.countError = data.countError;
                            if(data.ERROR)
                            {
                                $(".adm-info-message-title").append(data.ERROR);
                                $(".adm-info-message-red").show();
                            }
                            loadTask(objData);    
                        }
                    }
                    if(data.STATUS=="OK" && data.TASK=="YM")
                    {
                        if(data.STEP=="END" || stop)
                        {
                            $("#perc_text").html(data.MESSAGE);
                            $("#percent_val").html(data.PERCENT+"%");
                            $("#catalog_bar").html(data.SUCCESS);
                            if(data.ITOGO_ERROR)
                                $("#catalog_bar_error").html(data.ITOGO_ERROR);
                            prog = parseInt(data.PERCENT);
                            $('#progress_bar_inner').width(500 * prog / 100);
                            _this.text('<?=GetMessage("sotbit_yandex_start")?>');
                            _this.attr("id", "btn_start");
                            if(data.ERROR)
                            {
                                $(".adm-info-message-title").append(data.ERROR);
                                $(".adm-info-message-red").show();
                            }
                            BX.closeWait("#sotbit_yandex_edit", show);
                            stop = false;
                        }else{
                            $("#perc_text").html(data.MESSAGE);
                            $("#percent_val").html(data.PERCENT+"%");
                            $("#catalog_bar").html(data.SUCCESS);
                            if(data.ITOGO_ERROR)
                                $("#catalog_bar_error").html(data.ITOGO_ERROR);
                            prog = parseInt(data.PERCENT);
                            $('#progress_bar_inner').width(500 * prog / 100);
                            objData.countProduct = data.countProduct;
                            objData.countProductProcent = data.countProductProcent;
                            objData.countCat = data.countCat;
                            objData.countError = data.countError;
                            objData.page = data.page;
                            if(data.ERROR)
                            {
                                $(".adm-info-message-title").append(data.ERROR);
                                $(".adm-info-message-red").show();
                            }
                            loadTask(objData);
                        }
                            
                    }
                    if(data.STATUS=="OK" && data.TASK=="SEARCH")
                    {
                        if(data.STEP=="END" || stop)
                        {
                            $("#perc_text").html(data.MESSAGE);
                            $("#percent_val").html(data.PERCENT+"%");
                            $("#catalog_bar").html(data.SUCCESS);
                            if(data.ITOGO_ERROR)
                                $("#catalog_bar_error").html(data.ITOGO_ERROR);
                            prog = parseInt(data.PERCENT);
                            $('#progress_bar_inner').width(500 * prog / 100);
                            _this.text('<?=GetMessage("sotbit_yandex_start")?>');
                            _this.attr("id", "btn_start");
                            if(data.ERROR)
                            {
                                $(".adm-info-message-title").append(data.ERROR);
                                $(".adm-info-message-red").show();
                            }
                            BX.closeWait("#sotbit_yandex_edit", show);
                            stop = false;
                        }else{
                            $("#perc_text").html(data.MESSAGE);
                            $("#percent_val").html(data.PERCENT+"%");
                            $("#catalog_bar").html(data.SUCCESS);
                            if(data.ITOGO_ERROR)
                                $("#catalog_bar_error").html(data.ITOGO_ERROR);
                            prog = parseInt(data.PERCENT);
                            $('#progress_bar_inner').width(500 * prog / 100);
                            
                            objData.countProduct = data.countProduct;
                            objData.countProductProcent = data.countProductProcent;
                            objData.countCat = data.countCat;
                            objData.countError = data.countError;
                            objData.page = data.page;
                            objData.searchCount = data.searchCount;
                            objData.searchPage = data.searchPage;
                            if(data.ERROR)
                            {
                                $(".adm-info-message-title").append(data.ERROR);
                                $(".adm-info-message-red").show();
                            }
                            loadTask(objData);
                        }
                            
                    }
                    
                }   
                    
            })    
        }
    })
})
</script>
<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>