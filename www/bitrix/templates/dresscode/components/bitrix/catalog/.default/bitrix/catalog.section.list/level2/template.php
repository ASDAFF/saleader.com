<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true); ?>
<div id="nextSection">
    <div class="title"><?= GetMessage("SELECT_SECTION") ?></div>
    <ul>
        <? foreach ($arResult["SECTIONS"] as $arElement): ?>
            <? if ($arElement["ELEMENT_CNT"] > 0): ?>
                <li>
                    <span class="sectionLine">
                        <span class="sectionColumn">
                            <? if (empty($arElement["SELECTED"])): ?>
                                <a href="<?= $arElement["SECTION_PAGE_URL"] ?>">
                                    <?= $arElement["NAME"] ?>
                                </a>
                            <? else: ?>
                                <span class="selected">
                                    <?= $arElement["NAME"] ?>
                                </span>
                            <? endif ?>
                        </span>
                        <span class="sectionColumn last">
                            <? if (empty($arElement["SELECTED"])): ?>
                                <a href="<?= $arElement["SECTION_PAGE_URL"] ?>" class="cnt">
                                    <?= $arElement["ELEMENT_CNT"] ?>
                                </a>
                            <? else: ?>
                                <span class="cnt selected">
                                    <?= $arElement["ELEMENT_CNT"] ?>
                                </span>
                            <? endif ?>
                        </span>
		    			</span>
                </li>
            <? endif; ?>
        <? endforeach; ?>
    </ul>
</div>
	
