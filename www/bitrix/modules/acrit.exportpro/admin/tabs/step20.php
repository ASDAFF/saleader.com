<?php
IncludeModuleLangFile(__FILE__);

$marketCategory = new CExportproMarketTiuDB();
$marketCategory = $marketCategory->GetList();
if( !is_array( $marketCategory ) )
    $marketCategory = array();

$validCategories = array();

foreach( $marketCategory as $market ){
    if( is_array( $arProfile["MARKET_CATEGORY"]["TIU"]["CATEGORY_LIST"] ) ){
        foreach( $arProfile["MARKET_CATEGORY"]["TIU"]["CATEGORY_LIST"] as $catId => $catValue ){
            if(  trim( $catValue ) == trim( $market["NAME"] ) )
                $validCategories[] = $catId;
        }
    }
}
    
$variantChecked = ( $arProfile["USE_VARIANT"] == "Y" ) ? 'checked="checked"' : "";

/*
$categories = $profileUtils->GetSections( $arProfile["IBLOCK_ID"], true );

$categoriesNew = array();
foreach( $categories as $depth ){
    $categoriesNew = array_merge( $categoriesNew, $depth );
}

$categories = $categoriesNew;

unset( $categoriesNew );
asort( $categories );
*/

$use_market_category = ( $arProfile["USE_MARKET_TIU_CATEGORY"] == "Y" ) ? 'checked="checked"' : "";
?>

<tr>
    <td width="40%" class="adm-detaell-l">
        <span id="hint_PROFILE[USE_MARKET_CATEGORY]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[USE_MARKET_CATEGORY]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_STEP1_USE_MARKETCATEGORY_HELP" )?>' );</script>
        <label for="PROFILE[USE_MARKET_TIU_CATEGORY]"><?=GetMessage( "ACRIT_EXPORTPRO_STEP1_USE_MARKETCATEGORY" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[USE_MARKET_TIU_CATEGORY]" <?=$use_market_category?> value="Y" />
        <i><?=GetMessage( "ACRIT_EXPORTPRO_STEP1_USE_MARKETCATEGORY_DESC" )?></i>
    </td>
</tr>
<tr>
	<td colspan="2" id="market_category_data">
		<table width="100%">
			<?foreach( $categories as $cat ){
			    if( $arProfile["CHECK_INCLUDE"] == "Y" ){
				    if( !in_array( $cat["ID"], $arProfile["CATEGORY"] ) ){
					    continue;
				    }
				}
				else{
				    if( !in_array( $cat["PARENT_1"], $arProfile["CATEGORY"] ) ){
						continue;
				    }
				}?>
				<tr>
					<td width="40%">
						<label form="PROFILE[MARKET_CATEGORY][TIU][CATEGORY_LIST][<?=$cat["ID"]?>]"><?=$cat["NAME"]?></label>
					</td>
					<td>
						<?$catVal = "";
						if( in_array( $cat["ID"], $validCategories ) )
							$catVal = $arProfile["MARKET_CATEGORY"]["TIU"]["CATEGORY_LIST"][$cat["ID"]];?>
                        <input type="text" value="<?=$catVal?>" name="PROFILE[MARKET_CATEGORY][TIU][CATEGORY_LIST][<?=$cat["ID"]?>]" />
                        
						<span class="field-edit" onclick="ShowMarketCategoryList( <?=$cat["ID"]?>, 'market_category_list_tiu' )" style="cursor: pointer"></span>
					</td>
				</tr>
			<?}?>
		</table>
		
		<div id="market_category_list_tiu" style="display: none">
        	<input onkeyup="FilterMarketCategoryList( this, 'market_category_list_tiu' )">
        	<select onchange="SetMarketCategoryTiu( this.value, this )" size="25">
        		<option></option>
        		<?foreach( $marketCategory as $marketCat ){?>
        			<option data-search="<?=strtolower( $marketCat["NAME"] )?>"><?=$marketCat["NAME"]?></option>
        		<?}?>
        	</select>
        </div>
	</td>
</tr>
