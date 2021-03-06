	<div class="mainTool">
		<?if(!empty($arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"])):?>
		<div class="article">
				<?=GetMessage("CATALOG_ART_LABEL")?><span class="changeArticle" data-first-value="<?=$arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?>"><?=$arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?></span>
			</div>
		<?endif;?>		
		<a class="price changePrice"><?=$arResult["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?>
			<?if(!empty($arResult["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"]) && $arResult["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] > 0):?>
				<s class="discount"><?=$arResult["MIN_PRICE"]["PRINT_VALUE"]?></s>
			<?endif;?>
		</a>
		<div class="row">
			<a href="#" class="addCart changeID changeCart<?if($arResult["CAN_BUY"] != true && $arResult["CATALOG_QUANTITY"] <= 0):?> disabled<?endif;?>" data-id="<?=$arResult["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ADDCART_LABEL")?>" class="icon"><?=GetMessage("ADDCART_LABEL")?></a>
		</div>
	</div>
	<div class="secondTool">
		<?if(isset($arResult["PROPERTIES"]["RATING"]["VALUE"])):?>
			<div class="row">
			    <img src="<?=SITE_TEMPLATE_PATH?>/images/reviews.png" alt="" class="icon">
			    <span class="label<?if(count($arResult["REVIEWS"]) > 0):?> countReviewsTools<?endif;?>"><?=GetMessage("REVIEWS_COUNT")?> <?=count($arResult["REVIEWS"])?></span>
			    <div class="rating">
			      <i class="m" style="width:<?=($arResult["PROPERTIES"]["RATING"]["VALUE"] * 100 / 5)?>%"></i>
			      <i class="h"></i>
			    </div>
			</div>
	    <?endif;?>	
	    <?if($arParams["SHOW_REVIEW_FORM"]):?>
	   	<div class="row">
	   		<a href="#" class="reviewAddButton label"><img src="<?=SITE_TEMPLATE_PATH?>/images/addReviewSmall.png" alt="<?=GetMessage("REVIEWS_ADD")?>" class="icon"><?=GetMessage("REVIEWS_ADD")?></a>
	   	</div>
	    <?endif;?>
		<div class="row">
			<a href="#" class="fastBack label changeID" data-id="<?=$arResult["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/fastBack.png" alt="<?=GetMessage("FASTBACK_LABEL")?>" class="icon"><?=GetMessage("FASTBACK_LABEL")?></a>
		</div>
		<div class="row">
			<a href="#" class="addWishlist label" data-id="<?=$arResult["~ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/wishlist.png" alt="<?=GetMessage("WISHLIST_LABEL")?>" class="icon"><?=GetMessage("WISHLIST_LABEL")?></a>
		</div>
		<div class="row">
			<a href="#" class="addCompare label changeID" data-id="<?=$arResult["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/compare.png" alt="<?=GetMessage("COMPARE_LABEL")?>" class="icon"><?=GetMessage("COMPARE_LABEL")?></a>
		</div>
		<div class="row">
			<?if($arResult["CATALOG_QUANTITY"] > 0):?>
				<a class="inStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="<?=GetMessage("AVAILABLE")?>" class="icon"><?=GetMessage("AVAILABLE")?></a>
			<?else:?>
				<?if($arResult["CAN_BUY"] == true):?>
					<a class="onOrder label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/onOrder.png" alt="<?=GetMessage("ON_ORDER")?>" class="icon"><?=GetMessage("ON_ORDER")?></a>
				<?else:?>
					<a class="outOfStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/outOfStock.png" alt="<?=GetMessage("NO_AVAILABLE")?>" class="icon"><?=GetMessage("NO_AVAILABLE")?></a>
				<?endif;?>
			<?endif;?>	
		</div>	
	</div>