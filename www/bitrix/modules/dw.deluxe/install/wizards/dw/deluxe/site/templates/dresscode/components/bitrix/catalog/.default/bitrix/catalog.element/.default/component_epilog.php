<?$APPLICATION->SetAdditionalCSS($templateFolder."/css/review.css");?>
<?$APPLICATION->SetAdditionalCSS($templateFolder."/css/media.css");?>
<?$APPLICATION->SetAdditionalCSS($templateFolder."/css/set.css");?>

<?$APPLICATION->AddHeadScript($templateFolder."/js/morePicturesCarousel.js");?>
<?$APPLICATION->AddHeadScript($templateFolder."/js/pictureSlider.js");?>
<?$APPLICATION->AddHeadScript($templateFolder."/js/zoomer.js");?>
<?$APPLICATION->AddHeadScript($templateFolder."/js/tabs.js");?>
<?$APPLICATION->AddHeadScript($templateFolder."/js/sku.js");?>

<?
	if($arResult['DETAIL_PAGE_URL'] !== $arResult['DETAIL_PAGE_URL_TMP']){
		$APPLICATION->AddHeadString('<link href="'.$arResult['DETAIL_PAGE_URL'].'" rel="canonical" />', true);
	}
?>