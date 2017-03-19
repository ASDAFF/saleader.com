<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?$this->setFrameMode(true);?>
<?if(!empty($arResult["PRICES"])):?>
	<div id="appProductPriceVariant">
		<div class="priceVariantHeading"><?=GetMessage("APP_PRODUCT_PRICE_VARIANT_HEADING")?><a href="#" class="appPriceVariantExit"></a></div>
		<div class="priceVariantList">
			<?foreach ($arResult["PRICES"] as $inp => $arNextPrice):?>
				<div class="priceVariantListItem">
					<div class="priceVariantListItemTable">
						<div class="priceVariantListItemRow">
							<div class="priceVariantListItemColumn"><?if(!empty($arNextPrice["MIN_AVAILABLE_PRICE"])):?><span class="minAvailablePrice"><?=GetMessage("APP_PRODUCT_PRICE_VARIANT_AVAILABLE_PRICE_LABEL")?></span><?else:?><?=$arNextPrice["NAME"]?><?endif;?></div>
							<div class="priceVariantListItemColumn"><?if(!empty($arNextPrice["MIN_AVAILABLE_PRICE"])):?><span class="minAvailablePrice"><?endif;?><?=$arNextPrice["DISCOUNT_PRICE_FORMATED"]?><?if($arNextPrice["DISCOUNT_PRICE"] < $arNextPrice["PRICE"]):?><s class="discount"><?=$arNextPrice["PRICE_FORMATED"]?></s><?endif;?><?if(!empty($arNextPrice["MIN_AVAILABLE_PRICE"])):?></span><?endif;?></div>
						</div>
					</div>
				</div>
			<?endforeach;?>
		</div>
		<a href="<?=SITE_DIR?>prices-info/" class="linkMore"><?=GetMessage("APP_PRODUCT_PRICE_VARIANT_LINK_MORE")?></a>
	</div>
	<link rel="stylesheet" href="<?=$templateFolder?>/ajax_styles.css">
<?endif;?>