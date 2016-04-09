<table class="productTable">
	<thead>
		<tr>
			<th><?=GetMessage("TOP_IMAGE")?></th>
			<th><?=GetMessage("TOP_NAME")?></th>														
			<th><?=GetMessage("TOP_QTY")?></th>
			<th><?=GetMessage("TOP_AVAILABLE")?></th>
			<th><?=GetMessage("TOP_PRICE")?></th>
			<th><?=GetMessage("TOP_SUM")?></th>													
			<th><?=GetMessage("TOP_DELETE")?></th>
		</tr>
	</thead>
	<tbody>
		<?foreach ($arResult["ITEMS"] as $key => $arElement):?>
		<?$countPos += $arElement["QUANTITY"] ?>
			<tr class="basketItemsRow parent" data-id="<?=$arElement["ID"]?>">
				<td><a href="<?=$arElement["INFO"]["DETAIL_PAGE_URL"]?>" target="_blank" class="pic"><img src="<?=!empty($arElement["INFO"]["PICTURE"]["src"]) ? $arElement["INFO"]["PICTURE"]["src"] : SITE_TEMPLATE_PATH."/images/empty.png"?>" alt="<?=$arElement["INFO"]["NAME"]?>"></a></td>
				<td class="name"><a href="<?=$arElement["INFO"]["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arElement["INFO"]["NAME"]?></a></td>
				<td class="bQty">		
					<div class="basketQty">
						<a href="#" class="minus" data-id="<?=$arElement["ID"]?>"></a>
							<input name="qty" type="text" value="<?=intVal($arElement["QUANTITY"])?>" class="qty" data-id="<?=$arElement["ID"]?>" />
							<a href="#" class="plus" data-id="<?=$arElement["ID"]?>"></a> 
						</div>
					</td>
				<td>                            
					<?if(/*$arElement["INFO"]["CATALOG_QUANTITY"] > 0*/$arElement["INFO"]["CAN_BUY"] ):?>
						<a class="inStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="" class="icon"><?=GetMessage("AVAILABLE")?></a>
					<?else:?>
						<?if($arElement["INFO"]["CAN_BUY"] == true):?>
							<a class="onOrder label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/onOrder.png" alt="" class="icon"><?=GetMessage("ON_ORDER")?></a>
						<?else:?>
							<a class="outOfStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/outOfStock.png" alt="" class="icon"><?=GetMessage("NOAVAILABLE")?></a>
						<?endif;?>
					<?endif;?>		
        		</td>
				<td>
					<span class="price">		      
						<?=($arElement["INFO"]["OLD_PRICE"] != $arElement["PRICE"] ? '<s>'.FormatCurrency($arElement["INFO"]["OLD_PRICE"], $OPTION_CURRENCY).'</s>' : '')?>
  						<?=FormatCurrency($arElement["PRICE"], $OPTION_CURRENCY);?> 
  					</span>
  				</td>
  				<td>
  					<span class="sum" data-price="<?=round($arElement["PRICE"])?>"><?=FormatCurrency($arElement["PRICE"] * round($arElement["QUANTITY"]), $OPTION_CURRENCY);?> </span>
  				</td>
				<td class="elementDelete"><a href="#" class="delete" data-id="<?=$arElement["ID"]?>"></a></td>
			</tr>
		<?endforeach;?>
	</tbody>
</table>