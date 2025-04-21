<?php B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
?>

<?php
$APPLICATION->IncludeComponent(
    'bitrix:main.ui.filter',
    '',
    [
        'FILTER_ID' => $arResult['filterId'],
        'GRID_ID' => $arResult['grid']['GRID_ID'],
        'FILTER' => [
            ['id' => 'TITLE', 'name' => 'Заголовок', 'type' => 'string', 'default' => true],
        ],
        'ENABLE_LABEL' => true,
        'ENABLE_LIVE_SEARCH' => true,
    ],
    $component
);
?>

<?
$APPLICATION->IncludeComponent(
	'bitrix:main.ui.grid',
	'',
	$arResult["grid"],
	$component
);
?>