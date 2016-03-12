<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<ul id="topService">
	<?if($arResult["FORM_TYPE"] == "login"):?>
		<li><a href="<?=SITE_DIR?>auth/?backurl=<?=$APPLICATION->GetCurPageParam();?>"><?=GetMessage("LOGIN")?></a></li>
		<li><a href="<?=SITE_DIR?>auth/?register=yes&amp;backurl=<?=$APPLICATION->GetCurPageParam();?>"><?=GetMessage("REGISTER")?></a></li>
	<?else:?>
		<li><a href="<?=SITE_DIR?>personal/"><?=GetMessage("PERSONAL")?></a></li>
		<li><a href="<?=SITE_DIR?>exit/"><?=GetMessage("EXIT")?></a></li>
	<?endif?>
<!-- 
	<li><a href="<?=SITE_DIR?>catalog/"><?=GetMessage("CATALOG")?></a></li> -->

</ul>