<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

use Bitrix\Main\Localization\Loc as Loc;

if (isset($_REQUEST['admin']) && $_REQUEST['admin'] === 'Y')
	define('ADMIN_SECTION', true);
if (isset($_REQUEST['site']) && !empty($_REQUEST['site']))
{
	$strSite = substr((string)$_REQUEST['site'], 0, 2);
	define('SITE_ID', $strSite);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
Loc::loadMessages(__FILE__);

global $APPLICATION;

if(!CModule::IncludeModule('sotbit.yandex'))
{
	//echo Loc::getMessage("BT_COMP_MLI_AJAX_ERR_MODULE_ABSENT");
	die();
}

CUtil::JSPostUnescape();

$KEY_API = COption::GetOptionString("sotbit.yandex", "KEY", "");

$sotbitYandex = new CSotbitYandex($KEY_API);

$arData = $sotbitYandex->searchRegion($_REQUEST["search"]);

$arResult = array();
if(!empty($arData->georegions->items))
{
    foreach($arData->georegions->items as $arItem)
    {
        $arResult[] = array("NAME"=>$arItem->name, "ID"=>$arItem->id);
    }
}

Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
echo CUtil::PhpToJsObject($arResult);
die();
?>