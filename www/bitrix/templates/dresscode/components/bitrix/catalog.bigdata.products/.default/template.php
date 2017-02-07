<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$frame = $this->createFrame()->begin("");

$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
);

$injectId = $arParams['UNIQ_COMPONENT_ID'];

if (isset($arResult['REQUEST_ITEMS']))
{
	// code to receive recommendations from the cloud
	CJSCore::Init(array('ajax'));

	// component parameters
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedParameters = $signer->sign(
		base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
		'bx.bd.products.recommendation'
	);
	$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');

	?>

	<div id="<?=$injectId?>"></div>

	<script type="text/javascript">
		BX.ready(function(){
			bx_rcm_get_from_cloud(
				'<?=CUtil::JSEscape($injectId)?>',
				<?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>,
				{
					'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
					'template': '<?=CUtil::JSEscape($signedTemplate)?>',
					'site_id': '<?=CUtil::JSEscape(SITE_ID)?>',
					'rcm': 'yes'
				}
			);
		});
	</script>
	<?
	$frame->end();
	return;

	// \ end of the code to receive recommendations from the cloud
}?>

<?if (!empty($arResult['ITEMS'])):?>
	<div id="<?=$injectId?>_items">
		<div class="tab item" id="fcBigData">
			<div id="bigDataCarousel">
				<div class="wrap">
					<ul class="slideBox productList">
						<?foreach ($arResult['ITEMS'] as $key => $arNextElement):?>
							<li>
								<div class="item product">
									<div class="tabloid">
										<?if(!empty($arNextElement["PROPERTIES"]["OFFERS"]["VALUE"])):?>
											<div class="markerContainer">
												<?foreach ($arNextElement["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker):?>
												    <div class="marker" style="background-color: <?=strstr($arNextElement["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arNextElement["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242"?>"><?=$marker?></div>
												<?endforeach;?>
											</div>
										<?endif;?>
										<?if(isset($arNextElement["PROPERTIES"]["RATING"]["VALUE"])):?>
										    <div class="rating">
										      <i class="m" style="width:<?=($arNextElement["PROPERTIES"]["RATING"]["VALUE"] * 100 / 5)?>%"></i>
										      <i class="h"></i>
										    </div>
									    <?endif;?>
										<a href="<?=$arNextElement["DETAIL_PAGE_URL"]?>" class="picture">
											<img src="<?=(!empty($arNextElement["IMAGE"]["src"]) ? $arNextElement["IMAGE"]["src"] : SITE_TEMPLATE_PATH.'/images/empty.png')?>" alt="<?=$arNextElement["NAME"]?>">
											<span class="getFastView" data-id="<?=$arNextElement["ID"]?>"><?=GetMessage("FAST_VIEW_PRODUCT_LABEL")?></span>
										</a>
										<a href="<?=$arNextElement["DETAIL_PAGE_URL"]?>" class="name"><span class="middle"><?=$arNextElement["NAME"]?></span></a>
										<a class="price"><?=$arNextElement["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?>
											<?if(!empty($arNextElement["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"]) && $arNextElement["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] > 0):?>
												<s class="discount"><?=$arNextElement["MIN_PRICE"]["PRINT_VALUE"]?></s>
											<?endif;?>
										</a>
									</div>
								</div>
							</li>
						<?endforeach;?>
					</ul>
					<a href="#" class="bigDataBtnLeft"></a>
					<a href="#" class="bigDataBtnRight"></a>
				</div>
			</div>
		</div>
		<script>
			$("#bigDataCarousel").dwCarousel({
				leftButton: ".bigDataBtnLeft",
				rightButton: ".bigDataBtnRight",
				countElement: 8,
				resizeElement: true,
				resizeAutoParams: {
					1920: 6,
					1700: 5,
					1500: 4,
					1200: 3,
					850: 2
				}
			});	

			$(function(){
				dwLoadBigData("<?=GetMessage("CVP_TPL_MESS_RCM")?>", "<?=$injectId?>")
			});

		</script>
	</div>
<?endif;?>
