<?
$module_id = "sotbit.yandex";
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($POST_RIGHT>="R") :

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

IncludeModuleLangFile(__FILE__);
CJSCore::Init(array("jquery"));

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_SET")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && $POST_RIGHT=="W" && check_bitrix_sessid())
{
	$Update = $Update.$Apply;
	ob_start();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	ob_end_clean();
    
    
    if(strlen($RestoreDefaults)>0)
    {
        COption::RemoveOption("sotbit.yandex");
    }
    else
    {
        $val = $_REQUEST["KEY"];
        $valType = $_REQUEST["TYPE_SERVICE"];
        COption::SetOptionString("sotbit.yandex", "KEY", $val);
        COption::SetOptionString("sotbit.yandex", "TYPE_SERVICE", $valType);
    }
    if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());

	if(strlen($_REQUEST["back_url_settings"]) > 0)
	{
		if((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		else
			LocalRedirect($_REQUEST["back_url_settings"]);
	}
	else
	{
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
	}
}

?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();

$val = COption::GetOptionString("sotbit.yandex", "KEY", "");
$valType = COption::GetOptionString("sotbit.yandex", "TYPE_SERVICE", "");

//$arType["REFERENCE_ID"] = array("ym", "apisystem", "icsystem");
$arType["REFERENCE_ID"] = array("apisystem", "ym");
//$arType["REFERENCE"] = array("YANDEX.MARKET", "APISYSTEM", "ICSYSTEM");
$arType["REFERENCE"] = array("APISYSTEM", "YANDEX.MARKET");

?>
<tr>
    <td width="40%" nowrap valign="top" style="padding-top:10px;">
        <?=GetMessage("sotbit_yandex_type_service")?>:
    </td>
    <td>
        <?=SelectBoxFromArray("TYPE_SERVICE", $arType, $valType, '', '');?>
    </td>
</tr>
<tr>
        <td width="40%" nowrap valign="top" style="padding-top:10px;">
            <?//if(empty($valType) || $valType=="ym"):?>
            <?=GetMessage("sotbit_yandex_options_key")?>:
            <?//else:?>
            <?//=GetMessage("sotbit_yandex_options_key1")?>
            <?//endif;?>
        </td>
        <td width="60%">
            <input type="text" size="50" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="KEY">
            <div class="notes">
                <table cellspacing="0" cellpadding="0" border="0" class="notes">
                    <tbody>
                        <tr class="top">
                            <td class="left"><div class="empty"></div></td>
                            <td><div class="empty"></div></td>
                            <td class="right"><div class="empty"></div></td>
                        </tr>
                        <tr>
                            <td class="left"><div class="empty"></div></td>
                            <td class="content">
                                <?if($valType=="ym"):?>
                                <?=GetMessage("sotbit_yandex_options_descr1")?> <a href="http://feedback2.yandex.ru/api-market-content/key/?from=marketcontentapi" target="_blank"><?=GetMessage("sotbit_yandex_options_descr2")?></a> <?=GetMessage("sotbit_yandex_options_descr3")?><br><br>
                                <?=GetMessage("sotbit_yandex_options_descr4")?> <a href="http://legal.yandex.ru/market_api_content/" target="_blank"><?=GetMessage("sotbit_yandex_options_descr5")?></a><br><br>
                                <?=GetMessage("sotbit_yandex_options_descr6")?>
                                <?elseif(empty($valType) || $valType=="apisystem"):?>
                                <?=GetMessage("sotbit_yandex_options_apisystem_descr1")?>
                                <?else:?>
                                <?=GetMessage("sotbit_yandex_options_icsystem_descr1")?>
                                <?endif;?>
                            </td>
                            <td class="right"><div class="empty"></div></td>
                        </tr>
                        <tr class="bottom">
                            <td class="left"><div class="empty"></div></td>
                            <td><div class="empty"></div></td>
                            <td class="right"><div class="empty"></div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </td>
</tr>
<?

$tabControl->BeginNextTab();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
	<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?endif;?>
<script type="">
    $(document).ready(function(){
        $("#TYPE_SERVICE").on("change", function(e){
          $("input[name=Apply]").click();
        })
    })
</script>
