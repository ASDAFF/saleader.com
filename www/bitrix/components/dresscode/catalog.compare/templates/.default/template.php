<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); $this->setFrameMode(true);?>
<?if(!empty($arResult["ITEMS"])):?>
<div id="compare">
	<ul id="compareCheck">
		<?if(!empty($arResult["PROPERTIES"])):?>
			<?foreach($arResult["PROPERTIES"] as $index => $arValues):?>
				<?if($arValues["SORT"] <= 5000):?>
					<li>
						<input type="checkbox" name="<?=$arValues["ID"]?>" id="<?=$arValues["ID"]?>" value="Y">
						<label for="<?=$arValues["ID"]?>"><?=$arValues["NAME"];?></label>						
					</li>
				<?endif;?>
			<?endforeach;?>
		<?endif;?>
	</ul>
	<div id="compareTools">
		<a href="#" class="hide"><?=GetMessage("HIDE");?></a>
		<a href="#" class="show"><?=GetMessage("SHOW");?></a>
	</div>
	<div id="compareBlock">

		<table>
			<tr>
				<td class="left">
					<div class="wrap">
						<img src="<?=SITE_TEMPLATE_PATH?>/images/empty.png" alt="" class="compareImage">
						<div class="headingTools">
							<div class="leftTools">
								<a href="#" class="all"><?=GetMessage("ALLFEATURES");?></a>
								<a href="#" class="different"><?=GetMessage("DISTINGUISHED");?></a>
							</div>
						</div>
						<ul class="propList">
							<?if(!empty($arResult["PROPERTIES"])):?>
								<?foreach ($arResult["PROPERTIES"] as $index => $arProp):?>
									<li data-id="<?=$arProp["ID"]?>" title="<?=$arProp["NAME"]?>"><?=$arProp["NAME"]?></li>
								<?endforeach;?>
							<?endif;?>
						</ul>
					</div>
				</td>
				<td class="right">
					<div id="topScroll">
						<div id="fakeScroll"></div>
					</div>
					<div id="scrollTable">
						<ul>
							<?foreach($arResult["ITEMS"] as $index => $arElement):?>
								<?$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));?>
								<?$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array());?>
								<li>
									<div class="scrollElement" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
										<ins data-id="<?=$arElement["ID"]?>"></ins>
										<div class="imgBlock"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=!empty($arElement["PICTURE"]["src"]) ? $arElement["PICTURE"]["src"] : SITE_TEMPLATE_PATH."/images/empty.png" ?>" alt="<?=$arElement["NAME"]?>"></a></div>
										<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="name"><?=$arElement["NAME"]?></a>
										<span class="price"><?if(!empty($arElement["SKU_SHOW_FROM"])): echo GetMessage("FROM"); endif;?><?=$arElement["PRICE"];?></span>
										<a href="#" class="addCart<?if(!$arElement["ADDCART"]):?> disabled<?endif;?>" data-ibl="<?=$arElement["IBLOCK_ID"]?>" data-id="<?=$arElement["ID"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="" class="icon"><?=!empty($arElement["SKU"]) ? GetMessage("ADDSKU") : GetMessage("ADDCART")?></a>
									</div>	
									<ul class="propList check">
										<?if(!empty($arElement["PROPERTIES"])):?>
											<?foreach ($arElement["PROPERTIES"] as $index => $arProp):?>
												<li data-id="<?=$arProp["ID"]?>"><?=$arProp["DISPLAY_VALUE"]?></li>
											<?endforeach;?>
										<?endif;?>
									</ul>
								</li>
							<?endforeach;?>
						</ul>
					</div>
				</td>
			</tr>
		</table>
</div>
</div>
<?else:?>
	<div id="empty">
		<div class="emptyWrapper">
			<div class="pictureContainer">
				<img src="<?=SITE_TEMPLATE_PATH?>/images/emptyFolder.png" alt="<?=GetMessage("EMPTY_HEADING")?>" class="emptyImg">
			</div>
			<div class="info">
				<h3><?=GetMessage("EMPTY_HEADING")?></h3>
				<p><?=GetMessage("EMPTY_TEXT")?></p>
				<a href="<?=SITE_DIR?>" class="back"><?=GetMessage("MAIN_PAGE")?></a>
			</div>
		</div>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "emptyMenu", Array(
			"ROOT_MENU_TYPE" => "left",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => "",
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "left",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N",
			),
			false
		);?>
	</div>
<?endif;?>