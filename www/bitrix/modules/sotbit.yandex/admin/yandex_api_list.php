<? 
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\ExpressionField;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
//require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin.php');
Loader::includeModule("iblock");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");
$module_id = "sotbit.yandex";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");


global $USER, $USER_FIELD_MANAGER;

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT <= "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$yandex_table = "b_sotbit_yandex_list";

$sTableID = 'tbl_'.$yandex_table;
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);


$FilterArr = Array(
    "find",
    "find_id",
    "find_name",
    "find_active",
    "find_mode",
    "find_timestamp_1",
    "find_iblock_id",
    "find_task",
    "find_agent",
    "find_agent_time",
    "find_start_last_time_1",
    "find_end_last_time_1",
);

$lAdmin->InitFilter($FilterArr);

$arHeaders = array(array(
    'id' => 'ID',
    'content' => 'ID',
    'sort' => 'ID',
    'default' => true
));

$entity_data_class = "SotbitYandexTable";
$ufEntityId = $yandex_table;


// show all columns by default

$filterTitles = array('id'=>'ID');
$filterFields = array('find_id');
$filterValues = array();
$arFields = SotbitYandexTable::getMap();

$arDateFields = array("TIMESTAMP_X"=>TIMESTAMP_X, "START_LAST_TIME_X"=>"START_LAST_TIME_X", "END_LAST_TIME_X"=>"END_LAST_TIME_X");
$arPropsName["ID"] = "ID";
if(!empty($arFields))
{
    foreach($arFields as $code=>$arValue)
    {
        if($code=="ID" || !isset($arValue["title"])) continue;
        $array = array(
            'id' => $code,
            'content' => $arValue["title"],
            'sort' => $code,
            'default' => true
        );
        $arPropsName[$code] = $arValue["title"];
        $arHeaders = array_merge($arHeaders, array($array));
        $filterTitles[strtolower($code)] = $arValue["title"];
        $v = "find_".strtolower($code);
        $v = str_replace("_x", "", $v);
        if($arDateFields[$code])
        {
            $filterFields[] = $v."_1";
            $filterFields[] = $v."_2";
            continue 1;    
        }
        $filterFields[] = $v;
            
    }
}

$lAdmin->AddHeaders($arHeaders);

if (!in_array($by, $lAdmin->GetVisibleHeaderColumns(), true))
{
    $by = 'ID';
}

// add filter
$filter = null;

$filter = $lAdmin->InitFilter($filterFields);

if (!empty($find_id))
{
    $filterValues['ID'] = $find_id;
}

if(!empty($arFields))
{
    foreach($arFields as $code=>$arValue)
    {
        if($code=="ID" || isset($arDateFields[$code]) || !isset($arValue["title"])) continue;
        
        $v = "find_".strtolower($code);
        $v =  ${$v};
        if (!empty($v))
            $filterValues[$code] = $v;
    }
    
    foreach($arDateFields as $code=>$val)
    {
        $code1 = str_replace("_X", "", $code);
        $v1 = "find_".strtolower($code1)."_1";
        $v2 = "find_".strtolower($code1)."_2";
        $v1 =  ${$v1};
        $v2 =  ${$v2};
        if (!empty($v1))
            $filterValues[">=".$code] = $v1; 
        if (!empty($v2))
            $filterValues["<=".$code] = $v2;   
    }
}

// group actions
if($lAdmin->EditAction())
{
    foreach($FIELDS as $ID=>$arFields)
    {
        $ID = (int)$ID;
        if ($ID <= 0)
            continue;

        if(!$lAdmin->IsUpdated($ID))
            continue;

        $entity_data_class::update($ID, $arFields);
    }
}

if($arID = $lAdmin->GroupAction())
{
    if($_REQUEST['action_target']=='selected')
    {
        $arID = array();

        $rsData = $entity_data_class::getList(array(
            "select" => array('ID'),
            "filter" => $filterValues
        ));

        while($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }

    foreach ($arID as $ID)
    {
        $ID = (int)$ID;

        if (!$ID)
        {
            continue;
        }

        switch($_REQUEST['action'])
        {
            case "delete":
                $arAgent = CAgent::GetList(array(), array("NAME"=>"CSotbitYandex::startAgent(".$ID.");"))->Fetch();
                if($arAgent["ID"])
                    CAgent::Delete($arAgent["ID"]);
                $entity_data_class::delete($ID);
                break;
        }
    }
}

$arr = array('delete' => true);
$lAdmin->AddGroupActionTable($arr);

// select data
/** @var string $order */
$order = strtoupper($order);

$usePageNavigation = true;
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'excel')
{
    $usePageNavigation = false;
}
else
{
    $navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize(
        $sTableID,
        array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage().'?ID='.$ENTITY_ID)
    ));
    if ($navyParams['SHOW_ALL'])
    {
        $usePageNavigation = false;
    }
    else
    {
        $navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
        $navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
    }
}
$getListParams = array(
    'select' => $lAdmin->GetVisibleHeaderColumns(),
    'filter' => $filterValues,
    'order' => array($by => $order),
    "select" => array("*")
);
unset($filterValues);
if ($usePageNavigation)
{
    $getListParams['limit'] = $navyParams['SIZEN'];
    $getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
}
$rsData = new CAdminResult($entity_data_class::getList($getListParams), $sTableID);
if ($usePageNavigation)
{
    $totalCount = SotbitYandexTable::getList(array(
        'limit' =>null,
        'offset' => null,
        'select' => array(new ExpressionField('CNT', 'COUNT(1)')),
        "filter" => $getListParams['filter']
    ))->Fetch();
    $totalCount = (int)$totalCount["CNT"];
    $totalPages = ceil($totalCount/$getListParams['limit']);
    
    $rsData->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
    $rsData->NavRecordCount = $totalCount;
    $rsData->NavPageCount = $totalPages;
    $rsData->NavPageNomer = $navyParams['PAGEN'];
}
else
{
    $rsData->NavStart();
}
$rsIBlock = CIBlock::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()){
    $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
    $arIBlockFilter['REFERENCE'][] = "[".$arr["ID"]."] ".$arr["NAME"];
    $arIBlockFilter['REFERENCE_ID'][] = $arr["ID"];
}

$arTask = array("sect"=>GetMessage("sotbit_yandex_task_sect"), "ym"=>GetMessage("sotbit_yandex_task_ym"), "search"=>GetMessage("sotbit_yandex_task_search"));
// build list
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));
while($arRes = $rsData->NavNext(true, "f_"))
{
    $row = $lAdmin->AddRow($f_ID, $arRes);
    $USER_FIELD_MANAGER->AddUserFields($ufEntityId.$hlblock['ID'], $arRes, $row);
    $row->AddViewField("NAME", '<a href="sotbit.yandex_api_edit.php?ID='.$f_ID.'&amp;lang='.LANG.'" title="'.GetMessage("parser_act_edit").'">'.$f_NAME.'</a>');
    $row->AddCheckField("ACTIVE");
    $row->AddCheckField("AGENT");
    
    $row->AddSelectField("IBLOCK_ID", $arIBlock);
    
    $row->AddSelectField("TASK", $arTask);
    
    $can_edit = true;

    $arActions = array();

    $arActions[] = array(
        "ICON" => "edit",
        "TEXT" => GetMessage($can_edit ? "MAIN_ADMIN_MENU_EDIT" : "MAIN_ADMIN_MENU_VIEW"),
        "ACTION" => $lAdmin->ActionRedirect("sotbit.yandex_api_edit.php?ID=".$f_ID."&lang=".LANGUAGE_ID),
        "DEFAULT" => true
    );

    $arActions[] = array(
        "ICON"=>"delete",
        "TEXT" => GetMessage("sotbit_yandex_menu_delete"),
        "ACTION" => "if(confirm('".GetMessageJS('sotbit_yandex_menu_delete_text')."')) ".
            $lAdmin->ActionRedirect("sotbit.yandex_api_list.php?action=delete&ID=".$f_ID."&lang=".LANGUAGE_ID."&".bitrix_sessid_get())
    );

    $row->AddActions($arActions);
}


// view
$lAdmin->AddAdminContextMenu(array(array(
    "TEXT"    => GetMessage('sotbit_yandex_add_new'),
    "TITLE"    => GetMessage('sotbit_yandex_add_new'),
    "LINK"    => "sotbit.yandex_api_edit.php?lang=".LANGUAGE_ID,
    "ICON"    => "btn_new"
)));

$lAdmin->CheckListMode();
$arIBlockFilter['REFERENCE'][] = "";
$arIBlockFilter['REFERENCE_ID'][] = "";
$rsIBlock = CIBlock::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()){
    $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
    $arIBlockFilter['REFERENCE'][] = "[".$arr["ID"]."] ".$arr["NAME"];
    $arIBlockFilter['REFERENCE_ID'][] = $arr["ID"];
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$filter = new CAdminFilter(
    $sTableID."_filter_id",
    $filterTitles
);

?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>">
<?
    $filter->Begin();
    ?>
    <tr>
        <td>ID:</td>
        <td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"><?=ShowFilterLogicHelp()?></td>
    </tr>
    <tr>
        <td><?=$arPropsName["NAME"]?>:</td>
        <td><input type="text" name="find_name" size="47" value="<?echo htmlspecialchars($find_name)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
    </tr>
    <tr>
        <td><?=$arPropsName["ACTIVE"]?>:</td>
        <td><?
            $arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
            echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_active), GetMessage("MAIN_ALL"));
        ?>
        </td>
    </tr>
    <tr>
        <td><?=$arPropsName["MODE"]?>:</td>
        <td><?
            $arr = array("reference"=>array("debug", "work"), "reference_id"=>array("debug","work"));
            echo SelectBoxFromArray("find_mode", $arr, htmlspecialchars($find_mode), GetMessage("MAIN_ALL"));
        ?>
        </td>
    </tr>
    <tr>
        <td><?=$arPropsName["TIMESTAMP_X"]?><?=" (".FORMAT_DATE."):"?></td>
        <td><?echo CalendarPeriod("find_timestamp_1", $find_timestamp_1, "find_timestamp_2", $find_timestamp_2, "find_form","Y")?></td>
    </tr>
    <tr>
        <td><?=$arPropsName["IBLOCK_ID"]?>:</td>
        <td><?
        echo SelectBoxFromArray("find_iblock_id", $arIBlockFilter, "", "", "");
        ?></td>
    </tr>
    <tr>
        <td><?=$arPropsName["TASK"]?>:</td>
        <td><?
            $arr = array("reference"=>array(GetMessage("sotbit_yandex_task_sect"), GetMessage("sotbit_yandex_task_ym"), GetMessage("sotbit_yandex_task_search")), "reference_id"=>array("sect","ym", "search"));
            echo SelectBoxFromArray("find_task", $arr, htmlspecialchars($find_task), GetMessage("MAIN_ALL"));
        ?>
        </td>
    </tr>
    <tr>
        <td><?=$arPropsName["AGENT"]?>:</td>
        <td><?
            $arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
            echo SelectBoxFromArray("find_agent", $arr, htmlspecialchars($find_agent), GetMessage("MAIN_ALL"));
            ?>
        </td>
    </tr>
    <tr>
        <td><?=$arPropsName["AGENT_TIME"]?>:</td>
        <td><input type="text" name="find_agent_time" size="47" value="<?echo htmlspecialcharsbx($find_agent_time)?>"><?=ShowFilterLogicHelp()?></td>
    </tr>
    <tr>
        <td><?=$arPropsName["START_LAST_TIME_X"]?><?=" (".FORMAT_DATE."):"?></td>
        <td><?echo CalendarPeriod("find_start_last_time_1", $find_start_last_time_1, "find_start_last_time_2", $find_start_last_time_2, "find_form","Y")?></td>
    </tr>
    <tr>
        <td><?=$arPropsName["END_LAST_TIME_X"]?><?=" (".FORMAT_DATE."):"?></td>
        <td><?echo CalendarPeriod("find_end_last_time_1", $find_end_last_time_1, "find_end_last_time_2", $find_end_last_time_2, "find_form","Y")?></td>
    </tr>

    <?
    //$USER_FIELD_MANAGER->AdminListShowFilter($ufEntityId);
    $filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
    $filter->End();
?>
</form>
<?

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); 
?>