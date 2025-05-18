<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var array $arParams
 */

$APPLICATION->IncludeComponent(
    'bitrix:ui.sidepanel.wrapper',
    '.default',
    [
        'POPUP_COMPONENT_NAME' => 'exam31.ticket:examelements.info',
        'POPUP_COMPONENT_TEMPLATE_NAME' => '.default',
        'POPUP_COMPONENT_PARAMS' => [
            'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'] ?? null,
        ],
        'USE_UI_TOOLBAR' => 'Y',
        'PAGE_MODE' => false,
        'PAGE_MODE_OFF_BACK_URL' => $arResult['FOLDER'],
        'BUTTONS' => ['close'],
    ]
);
