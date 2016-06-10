<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<script type="text/javascript">
	var ajaxDir = "<?=$this->GetFolder();?>";
</script>
<?if(!empty($arResult["GROUPS"])):?>
	
	<?if(empty($arParams["AJAX"])):?>
		<div id="homeCatalog">
			<?if(!empty($arResult["PROPERTY_ENUM"])):?>
				<div class="captionList">
					<div id="captionCarousel">
						<ul class="slideBox">
							<?foreach ($arResult["PROPERTY_ENUM"] as $ipe => $arPropEnum):?>
								<?if(!empty($arResult["GROUPS"][$ipe]["ITEMS"])):?>
									<li class="cItem">
										<div class="caption<?if($arPropEnum["SELECTED"] == "Y"):?> selected<?endif;?>"><a href="#" data-name="<?=$arPropEnum["PROP_NAME"]?>" data-group="<?=$arPropEnum["ID"]?>" data-page="1" data-sheet="N" class="getProductByGroup"><?=$arPropEnum["VALUE"]?></a></div>
									</li>	
								<?endif;?>
							<?endforeach;?>
						</ul>
						<a href="#" class="captionBtnLeft"></a>
						<a href="#" class="captionBtnRight"></a>
					</div>
					<script type="text/javascript">
						$("#captionCarousel").dwCarousel({
							leftButton: ".captionBtnLeft",
							rightButton: ".captionBtnRight",
							countElement: 5,
							resizeElement: true,
							resizeAutoParams: {
								1920: 5,
								1800: 4,
								1550: 3,
								1100: 2,
								850: 1
							}
						});
					</script>
				</div>
			<?endif;?>
		<?endif;?> 
			<?foreach ($arResult["GROUPS"] as $itg => $arItemsGroup):?>
				<?if(!empty($arItemsGroup["ITEMS"])):?>
					<?if(empty($arParams["AJAX"])):?>
						<div class="ajaxContainer">
					<?endif;?>
						<div class="items productList">
							<?foreach ($arItemsGroup["ITEMS"] as $index => $arElement):?>
								<?
									$this->AddEditAction($arElement["ID"], $arElement["EDIT_LINK"], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_EDIT"));
									$this->AddDeleteAction($arElement["ID"], $arElement["DELETE_LINK"], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
								?>
								<div class="item product sku" id="<?=$this->GetEditAreaId($arElement["ID"]);?>" data-product-id="<?=!empty($arElement["~ID"]) ? $arElement["~ID"] : $arElement["ID"]?>" data-iblock-id="<?=$arElement["SKU_INFO"]["IBLOCK_ID"]?>" data-prop-id="<?=$arElement["SKU_INFO"]["SKU_PROPERTY_ID"]?>">
							
									<div class="tabloid">
										<?if(!empty($arElement["PROPERTIES"]["OFFERS"]["VALUE"])):?>
											<div class="markerContainer">
												<?foreach ($arElement["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker):?>
												    <div class="marker" style="background-color: <?=strstr($arElement["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arElement["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242"?>"><?=$marker?></div>
												<?endforeach;?>
											</div>
										<?endif;?>
										<?if(isset($arElement["PROPERTIES"]["RATING"]["VALUE"])):?>
										    <div class="rating">
										      <i class="m" style="width:<?=($arElement["PROPERTIES"]["RATING"]["VALUE"] * 100 / 5)?>%"></i>
										      <i class="h"></i>
										    </div>
									    <?endif;?>
										<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="picture"><img src="<?=(!empty($arElement["IMG"]["src"]) ? $arElement["IMG"]["src"] : SITE_TEMPLATE_PATH.'/images/empty.png')?>" alt="<?=$arElement["NAME"]?>"></a>
										<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="name"><span class="middle"><?=$arElement["NAME"]?></span></a>
										<a class="price"><?=CCurrencyLang::CurrencyFormat($arElement["PRICE"]["DISCOUNT_PRICE"], $arResult["CURRENCY"], true)?>
											<?if(!empty($arElement["PRICE"]["DISCOUNT"])):?>
												<s class="discount"><?=CCurrencyLang::CurrencyFormat($arElement["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["CURRENCY"], true)?></s>
											<?endif;?>
										</a>
										<a href="#" class="addCart<?if($arElement["CAN_BUY"] != true && $arElement["CATALOG_QUANTITY"] <= 0):?> disabled<?endif;?>" data-id="<?=$arElement["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="" class="icon"><?=GetMessage("ADDCART_LABEL")?></a>
										<div class="optional">
											<div class="row">
												<a href="#" class="fastBack label" data-id="<?=$arElement["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/fastBack.png" alt="" class="icon"><?=GetMessage("FASTBACK_LABEL")?></a>
												<a href="#" class="addCompare label" data-id="<?=$arElement["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/compare.png" alt="" class="icon"><?=GetMessage("COMPARE_LABEL")?></a>
											</div>
											<div class="row">
												<a href="#" class="addWishlist label" data-id="<?=$arElement["~ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/wishlist.png" alt="" class="icon"><?=GetMessage("WISHLIST_LABEL")?></a>
												<?if(/*$arElement["CATALOG_QUANTITY"] > 0*/$arElement["CAN_BUY"]):?>
													<a class="inStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="" class="icon"><?=GetMessage("AVAILABLE")?></a>
												<?else:?>
													<?if($arElement["CAN_BUY"] == true):?>
														<a class="onOrder label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/onOrder.png" alt="" class="icon"><?=GetMessage("ON_ORDER")?></a>
													<?else:?>
														<a class="outOfStock label changeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/outOfStock.png" alt="" class="icon"><?=GetMessage("NOAVAILABLE")?></a>
													<?endif;?>
												<?endif;?>
											</div>						
										</div>
										<?if(!empty($arElement["SKU_PRODUCT"])):?>
											<?if(!empty($arElement["SKU_PROPERTIES"]) && $level = 1):?>
												<?foreach ($arElement["SKU_PROPERTIES"] as $propName => $arNextProp):?>
													<?if(!empty($arNextProp["VALUES"])):?>
														<div class="skuProperty" data-name="<?=$propName?>" data-level="<?=$level++?>">
															<div class="skuPropertyName"><?=$arNextProp["NAME"]?></div>
															<ul class="skuPropertyList">
																<?foreach ($arNextProp["VALUES"] as $xml_id => $arNextPropValue):?>
																	<li class="skuPropertyValue<?if($arNextPropValue["DISABLED"] == "Y"):?> disabled<?elseif($arNextPropValue["SELECTED"] == "Y"):?> selected<?endif;?>" data-name="<?=$propName?>" data-value="<?=$arNextPropValue["VALUE"]?>">
																		<a href="#" class="skuPropertyLink">
																			<?if(!empty($arNextPropValue["IMAGE"])):?>
																				<img src="<?=$arNextPropValue["IMAGE"]["src"]?>">
																			<?else:?>
																				<?=$arNextPropValue["VALUE"]?>
																			<?endif;?>
																		</a>
																	</li>
																<?endforeach;?>
															</ul>
														</div>
													<?endif;?>
												<?endforeach;?>
											<?endif;?>
										<?endif;?>
									</div>
								</div>
							<?endforeach;?>
							<?if(!empty($arResult["HIDE_LAST_ELEMENT"])):?>
								<div class="item product last">
									<a href="#" class="showMore">
										<span class="wp">
											<span class="icon"><img src="<?=SITE_TEMPLATE_PATH?>/images/showMore.png" alt="<?=GetMessage("SHOW_MORE")?>"></span>
											<span class="ps"><?=GetMessage("SHOW_MORE")?></span><span class="value"><?=$arParams["NEXT_ELEMENTS_COUNT"]?></span>
											<span class="small"><?=GetMessage("SHOWS")?> <?=$arParams["~ELEMENTS_COUNT"]?> <?=GetMessage("FROM")?> <?=$arResult["FIRST_ITEMS_ALL_COUNT"]?></span>
										</span>
									</a>
								</div>
							<?endif;?>
							<div class="clear"></div>
						</div>
					<?if(empty($arParams["AJAX"])):?>
						</div>
					<?endif;?>
					<?break(1);?>
				<?endif;?>
			<?endforeach;?>
	<?if(empty($arParams["AJAX"])):?>
		</div>
	<?endif;?>

	<script type="text/javascript">
		var offersProductParams = '<?=json_encode($arParams);?>';
	</script>

<?endif;?>	