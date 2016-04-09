<div class="items productList">
	<?foreach ($arResult["ITEMS"] as $ix => $arElement):?>
		<?$countPos += $arElement["QUANTITY"]?>
		<div class="item product parent">
			<div class="tabloid">
			 	<div class="topSection">
					<div class="column">
						<?if(/*$arElement["INFO"]["CATALOG_QUANTITY"] > 0*/$arElement["INFO"]["CAN_BUY"]):?>
							<a class="inStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="" class="icon"><?=GetMessage("AVAILABLE")?></a>
						<?else:?>
							<?if($arElement["INFO"]["CAN_BUY"] == true):?>
								<a class="onOrder label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/onOrder.png" alt="" class="icon"><?=GetMessage("ON_ORDER")?></a>
							<?else:?>
								<a class="outOfStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/outOfStock.png" alt="" class="icon"><?=GetMessage("NOAVAILABLE")?></a>
							<?endif;?>
						<?endif;?>	
                    </div>
                    <div class="column">
						<a href="#" class="delete" data-id="<?=$arElement["ID"]?>"></a>
                    </div> 		
			 	</div>
			    <a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="picture"><img src="<?=!empty($arElement["INFO"]["PICTURE"]["src"]) ? $arElement["INFO"]["PICTURE"]["src"] : SITE_TEMPLATE_PATH."/images/empty.png"?>" alt="<?=$arElement["NAME"]?>"></a>
				<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="name"><span class="middle"><?=$arElement["NAME"]?></span></a>
				<a class="price">
					<?=FormatCurrency($arElement["PRICE"], $OPTION_CURRENCY);?>
  					<?=($arElement["INFO"]["OLD_PRICE"] != $arElement["PRICE"] ? '<s class="discount">'.FormatCurrency($arElement["INFO"]["OLD_PRICE"], $OPTION_CURRENCY).'</s>' : '')?>
  				</a>
				<div class="basketQty">
					<?=GetMessage("BASKET_QUANTITY_LABEL")?> <a href="#" class="minus" data-id="<?=$arElement["ID"]?>"></a>
					<input name="qty" type="text" value="<?=intVal($arElement["QUANTITY"])?>" class="qty" data-id="<?=$arElement["ID"]?>" />
					<a href="#" class="plus" data-id="<?=$arElement["ID"]?>"></a> 
				</div> 	
				<span class="sum hidden" data-price="<?=round($arElement["PRICE"])?>"><?=FormatCurrency($arElement["PRICE"] * round($arElement["QUANTITY"]), $OPTION_CURRENCY);?> </span>
			</div>
		</div>	
	<?endforeach;?>
	<div class="clear"></div>
</div>
