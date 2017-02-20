<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if(!empty($arResult)):?>
		<?$image = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width" => 650, "height" => 800), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
		<?if(!empty($image["src"])):?>
			<div class="bigPicture">
				<img src="<?=$image["src"]?>" alt="<?=$arResult["NAME"]?>">
			</div>
		<?endif;?>
		<?if(!empty($arResult["DETAIL_TEXT"])):?>
			<div class="description"><?=$arResult["DETAIL_TEXT"]?></div>
		<?endif;?>
<?endif;?>

