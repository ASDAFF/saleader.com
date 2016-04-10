<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);
?>
<?$BIG_PICTURE = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width" => 150, "height" => 250), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>

<?if(!empty($BIG_PICTURE["src"])):?>
    <div class="brandsBigPicture"><img src="<?=$BIG_PICTURE["src"]?>" alt="<?=$arResult["ITEM"]["NAME"]?>"></div>
<?endif;?>

<?if(!empty($arResult["DETAIL_TEXT"])):?>
    <div class="brandsDescription"><?=$arResult["DETAIL_TEXT"]?></div>
<?endif;?>

