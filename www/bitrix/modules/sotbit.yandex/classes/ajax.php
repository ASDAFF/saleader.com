<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);
set_time_limit(0);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

global $APPLICATION;

if(!CModule::IncludeModule('sotbit.yandex'))
{
    die();
}
$KEY_API = COption::GetOptionString("sotbit.yandex", "KEY", "");
$arResult = array();

$sotbitYandex = new CSotbitYandex($KEY_API);
$APPLICATION->RestartBuffer();
if($_REQUEST["TYPE"]=="setCat")
{
    if(isset($_REQUEST["CAT"]["VALUES"]))
    {
        foreach($_REQUEST["CAT"]["VALUES"] as $val)
        {
            $_SESSION["SOTBIT_YA_CAT"][] = $val;
        }
    }    
}elseif($_REQUEST["TYPE"]=="loadCat" && isset($_REQUEST["catID"]) && $_REQUEST["catID"]>0)
{
    $arCat = explode("|", $_REQUEST["catID"]);
    $arData = $sotbitYandex->getListChildCategory(213, $arCat[0]);
    $strSelect = "";
    if($arData->categories->items)
    {
        foreach($arData->categories->items as $data)
        {
            
            $strSelect .= '<option data-depth="'.$_REQUEST["depth"].'" value="'.$data->id.'|'.$_REQUEST["depth"].'|'.$data->name.'">'.str_repeat(" . ", $_REQUEST["depth"])." ".$data->name.'</option>';    
        }    
    }
    echo $strSelect;    
}elseif($_REQUEST["TYPE"]=="fileProp")
{   
    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_REQUEST['iblock'], "PROPERTY_TYPE"=>"F"));
    echo '<option value="0">'.GetMessage("sotbit_yandex_product_more_picture_0").'</option>';
    while($arProp = $properties->Fetch())
    {
        echo '<option value="'.$arProp["CODE"].'">'."[".$arProp["CODE"]."] ".$arProp["NAME"].'</option>';
    }
}elseif($_REQUEST["TYPE"]=="stringProp")
{   
    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_REQUEST['iblock'], "PROPERTY_TYPE"=>"S"));
    echo '<option value="0">'.GetMessage("sotbit_yandex_product_url_0").'</option>';
    while($arProp = $properties->Fetch())
    {
        echo '<option value="'.$arProp["CODE"].'">'."[".$arProp["CODE"]."] ".$arProp["NAME"].'</option>';
    }
}
elseif(($_REQUEST["TYPE"]=="start" || $_REQUEST["TYPE"]=="current") && isset($_REQUEST["ID"]))
{
    $sotbitYandex->startTask($_REQUEST["ID"]);
}
die();
?>