<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

Extension::load("ui.sidepanel-content");

/**
 * @var array $arParams
 * @var array $arResult
 */
?>

<div class="ui-slider-section">
    <div class="ui-slider-content-box">
        <div class="ui-slider-heading-3"><?= Loc::getMessage('EXAM31_INFO_HEADING_TEXT') ?></div>
    </div>
    <div class="ui-slider-content-box">
        <? if (!empty($arResult['ITEMS'])) { ?>
            <? foreach ($arResult['ITEMS'] as $item) { ?>
                <div class="ui-slider-frame --no-hover">
                    <div class="ui-slider-heading-3"><?= htmlspecialcharsbx($item['ID']) ?></div>
                    <div class="ui-slider-paragraph-2"><?= htmlspecialcharsbx($item['TITLE']) ?></div>
                </div>
            <? } ?>
        <? } else { ?>
            <div class="ui-slider-heading-3"><?= Loc::getMessage('EXAM31_INFO_NOT_EXIST_TEXT') ?></div>
        <? } ?>
    </div>
</div>
