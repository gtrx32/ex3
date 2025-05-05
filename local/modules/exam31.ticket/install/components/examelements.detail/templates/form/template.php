<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 */
?>

<?
$request = \Bitrix\Main\Context::getCurrent()->getRequest();

if ($request->get('IFRAME') === 'Y') {
    $APPLICATION->ShowHead();
}

$APPLICATION->IncludeComponent(
    'bitrix:ui.form',
    '.default',
    $arResult['form']
);
?>

<p class="ui-slider-paragraph">
    <a href="javascript:void(0);" onclick="BX.SidePanel.Instance.close();">
        <?= Loc::getMessage('EXAM31_ELEMENT_DETAIL_BACK_TO_LIST') ?>
    </a>
</p>