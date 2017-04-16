<?php
IncludeModuleLangFile( __FILE__ );

if( CModule::IncludeModule( "currency" ) ){
    $dbCurrencyList = CCurrency::GetList(
        $by = "sort",
        $order = "asc",
        LANGUAGE_ID
    );

    $arCurrencyList = array();
    while( $arCurrency = $dbCurrencyList->Fetch() ){
        $arCurrencyList[$arCurrency["CURRENCY"]] = $arCurrency["FULL_NAME"];
    }
}
else{
    $arCurrencyList = array(
        "RUB" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_RUB" ),
        "USD" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_USD" ),
        "EUR" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_EUR" ),
        "UAH" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_UAH" ),
        "BYR" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_BYR" ),
        "KZT" => GetMessage( "ACRIT_EXPORTPRO_CURRENCU_KZH" )
    );
}

$convertCurrency = ( $arProfile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ) ? 'checked="checked"' : "";
$convertTable = $convertCurrency ? "" : "display: none;";
$correctTable = $convertTable ? "" : "display: none;";

$currencyRates = CExportproProfile::LoadCurrencyRates();



$bCurrencyIntersect = false;
$arCurrencyRatesTmp = array();
foreach( $currencyRates as $currencyRatesIndex => $currencyRatesItem ){
    if( is_array( $currencyRatesItem ) ){
        if( !$bCurrencyIntersect ){
            $arCurrencyRatesTmp = $currencyRatesItem;
            $bCurrencyIntersect = true;
        }
        else{
            $arCurrencyRatesTmp = array_intersect( $arCurrencyRatesTmp, $currencyRatesItem );
        }
    }
}
$currencyRates = $arCurrencyRatesTmp;

if( empty( $currencyRates ) ){
    $currencyRates = $arCurrencyList;
}
ksort( $currencyRates );

$idConvertCnt = 0;
?>

<tr class="heading">
    <td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_CONDITIONS" )?></td>
</tr>
<tr>
    <td colspan="2">
        <div id="PROFILE_CONDITION">
            <?
                $obCond = new CAcritExportproCatalogCond();
                CAcritExportproProps::$arIBlockFilter = $obProfileUtils->PrepareIBlock(
                    $arProfile["IBLOCK_ID"],
                    $arProfile["USE_SKU"]
                );

                $boolCond = $obCond->Init(
                    BT_COND_MODE_DEFAULT,
                    BT_COND_BUILD_CATALOG,
                    array(
                        "FORM_NAME" => "exportpro_form",
                        "CONT_ID" => "PROFILE_CONDITION",
                        "JS_NAME" => "JSCatCond",
                        "PREFIX" => "PROFILE[CONDITION]"
                    )
                );

                if( !$boolCond ){
                    if( $ex = $APPLICATION->GetException() ){
                        echo $ex->GetString()."<br/>";
                    }
                }

                $obCond->Show( $arProfile["CONDITION"] );
            ?>
        </div>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU" )?></td>
</tr>
<tr>
    <td width="50%">
        <span id="hint_PROFILE[CURRENCY][CONVERT_CURRENCY]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[CURRENCY][CONVERT_CURRENCY]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_CURRENCY_CONVERT_CURRENCY_HELP" )?>' );</script>
        <label for="PROFILE[CURRENCY][CONVERT_CURRENCY]"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCY_CONVERT_CURRENCY" )?></label>
    </td>
    <td><input type="checkbox" name="PROFILE[CURRENCY][CONVERT_CURRENCY]" value="Y" <?=$convertCurrency?> onclick="convertCurrency()" ></td>
</tr>
<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CURRENCY_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <table cellpadding="2" cellspacing="0" border="0" class="internal" align="center" width="100%">
            <thead>
                <tr class="heading">
                    <td colspan="3" align="left"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU_HEAD_CURRENCY" )?></td>
                    <td align="center" class="currency_table" style="<?=$convertTable?>"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU_HEAD_RATE" )?></td>
                    <td align="center" class="currency_table" style="<?=$convertTable?>"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU_HEAD_CONVERTTO" )?></td>
                    <td align="center"><?=GetMessage( "ACRIT_EXPORTPRO_CURRENCU_HEAD_CORRECT" )?></td>
                </tr>
            </thead>
            <tbody>
                <?if( is_array( $arCurrencyList ) ){
                    foreach( $arCurrencyList as $id => $curr ){
                        $checked = $arProfile["CURRENCY"][$id]["CHECK"] == "Y" ? 'checked="checked"' : "";?>
                        <tr>
                            <td align="center"><input type="checkbox" name="PROFILE[CURRENCY][<?=$id?>][CHECK]" value="Y" <?=$checked?> /></td>

                            <?$convertFrom = $arProfile["CURRENCY"][$id]["CONVERT_FROM"] ? $arProfile["CURRENCY"][$id]["CONVERT_FROM"] : $id?>
                            <td class="currency_table" style="<?=$convertTable?>">
                                <input type="text" name="PROFILE[CURRENCY][<?=$id?>][CONVERT_FROM]" value="<?=$convertFrom?>">
                            </td>

                            <td><?=$curr?></td>

                            <td align="center" class="currency_table" style="<?=$convertTable?>">
                                <select name="PROFILE[CURRENCY][<?=$id?>][RATE]">
                                    <?foreach( $obProfileUtils->GetCurrencyRate() as $rate => $name ){?>
                                        <?$selected = $rate == $arProfile["CURRENCY"][$id]["RATE"] ? 'selected="selected"' : "";?>
                                        <option value="<?=$rate?>" <?=$selected?>><?=$name?></option>
                                    <?}?>
                                </select>
                            </td>

                            <td class="currency_table" style="<?=$convertTable?>">
                                <select name="PROFILE[CURRENCY][<?=$id?>][CONVERT_TO]">
                                    <?foreach( $currencyRates as $currency ){?>
                                        <?$selected = $currency["CURRENCY"] == $arProfile["CURRENCY"][$id]["CONVERT_TO"] ? 'selected="selected"' : "";?>
                                        <option value="<?=$currency["CURRENCY"]?>" <?=$selected?>><?=$currency["CURRENCY"]?></option>
                                    <?}?>
                                </select>
                            </td>

                            <td align="center">+-<input type="text" name="PROFILE[CURRENCY][<?=$id?>][PLUS]" value="<?=$arProfile["CURRENCY"][$id]["PLUS"]?>">%</td>
                        </tr>
                    <?}
                }?>
            </tbody>
        </table>
    </td>
</tr>
<tr class="heading" align="center">
    <td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_CONVERT_FIELDSET_HEADER" )?></td>
</tr>
<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_EXPORTPRO_CONVERT_FIELDSET_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <table id="convert-fieldset-container" cellpadding="0" cellspacing="0" width="100%">
            <tbody>
                <?if( is_array( $arProfile["CONVERT_DATA"] ) ){
                    foreach( $arProfile["CONVERT_DATA"] as $id => $fields ){?>
                        <tr class="fieldset-item" data-id="<?=$idConvertCnt++?>">
                            <td align="right">
                                <input type="text" name="PROFILE[CONVERT_DATA][<?=$id?>][0]" value="<?=htmlspecialchars( $fields[0], ENT_QUOTES );?>" />
                            </td>
                            <td align="left" style="position: relative" class="adm-detail-content-cell-r">
                                <input type="text" name="PROFILE[CONVERT_DATA][<?=$id?>][1]" value="<?=htmlspecialchars( $fields[1], ENT_QUOTES );?>" />
                                <span class="fieldset-item-delete">&times</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr style="opacity: 0.2;">
                            </td>
                        </tr>
                    <?}
                }?>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td colspan="2" align="center" id="fieldset-item-add-button">
        <button class="adm-btn" onclick="ConvertFieldsetAdd( this ); return false;">
            <?=GetMessage( "ACRIT_EXPORTPRO_CONVERT_FIELDSET_CONDITION_ADD" )?>
        </button>
    </td>
</tr>