<?php

IncludeModuleLangFile(__FILE__);

if( $arProfile["USE_IBLOCK_CATEGORY"] == "Y" ){
    $categories = $iblocks;
}
elseif( $arProfile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
    $categories = $productIbCategories;
}

if( ( ( $arProfile["TYPE"] == "ebay_1" ) || ( $arProfile["TYPE"] == "ebay_2" ) ) && !empty( $categories ) ){
    $obMarketCategory = new CExportproMarketEbayDB();
    $arMarketCategory = $obMarketCategory->GetList();
    if( !is_array( $arMarketCategory ) )
        $arMarketCategory = array();

    $arNewMarketCategory = array();
    foreach( $arMarketCategory as $marketCat ){
        $arNewMarketCategory[$marketCat["id"]] = $marketCat;
    }

    $arMarketCategory = $arNewMarketCategory;
    unset( $arNewMarketCategory );

    for( $i = 2; $i < 100; $i++ ){
        $levelCnt = 0;
        foreach( $arMarketCategory as &$marketCat ){
            if( $marketCat["level"] == $i ){
                $levelCnt++;
                if( is_array( $arMarketCategory[$marketCat["parent_id"]] ) )
                    $marketCat["name"] = $arMarketCategory[$marketCat["parent_id"]]["name"]." / ".$marketCat["name"];
            }
        }
        if( $levelCnt == 0 )
            break;
    }

    $use_market_category = $arProfile["USE_MARKET_CATEGORY"] == "Y" ? 'checked="checked"' : "";
}
?>

<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_EBAY_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td colspan="2" id="market_category_data_ebay">
        <table width="100%">
            <?foreach( $categories as $cat ){?>
                <tr>
                    <td>
                        <label form="PROFILE[MARKET_CATEGORY][EBAY][CATEGORY_LIST][<?=$cat["ID"]?>]"><?=$cat["NAME"]?></label>
                    </td>
                    <td width="60%">
                        <input type="text" readonly="readonly" value="<?=$arMarketCategory[$arProfile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$cat["ID"]]]["name"]?>" name="PROFILE_MARKET_CATEGORY_CATEGORY_LIST_EBAY_<?=$cat["ID"]?>_NAME" style="width:100%; opacity:1"/>
                        <input type="hidden" value="<?=$arProfile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$cat["ID"]]?>" name="PROFILE[MARKET_CATEGORY][EBAY][CATEGORY_LIST][<?=$cat["ID"]?>]" />
                        <span class="field-edit" onclick="ShowMarketCategoryList( <?=$cat["ID"]?>, 'market_category_list_ebay' )" style="cursor: pointer; background: #9ec710 !important;" title="<?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_DATA_SELECT_SECTION" );?>"></span>
                    </td>
                </tr>
            <?}?>
        </table>
        <div id="market_category_list_ebay" style="display: none">
            <?if( is_array( $arMarketCategory ) && !empty( $arMarketCategory ) ){
                foreach( $arMarketCategory as $marketCat ){
                    $sortetMarketCategory[$marketCat["name"]] = $marketCat;
                }
                    
                $arMarketCategory = $sortetMarketCategory;
                unset( $sortetMarketCategory );
                ksort( $arMarketCategory );
            }?>
            
            <input onkeyup="FilterMarketCategoryList( this, 'market_category_list_ebay' )" placeholder="<?=GetMessage( "ACRIT_EXPORTPRO_MARKET_CATEGORY_DATA_WINDOW_PLACEHOLDER" );?>">
            <select onclick="SetMarketCategoryEbay( this.value, this )" size="25">
                <option></option>
                <?if( is_array( $arMarketCategory ) && !empty( $arMarketCategory ) ){
                    foreach( $arMarketCategory as $marketCat ){?>
                        <option value="<?=$marketCat["id"]?>" data-search="<?=strtolower( $marketCat["name"] )?>"><?=$marketCat["name"]?></option>
                    <?}
                }?>
            </select>
        </div>
    </td>
</tr>