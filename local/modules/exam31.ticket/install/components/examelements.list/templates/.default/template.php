<?php B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

use \Bitrix\UI\Toolbar\Facade\Toolbar;
?>

<?
Toolbar::addFilter([
    'GRID_ID' => $arResult['grid']['GRID_ID'],
    'FILTER_ID' => $arResult['filterId'],
    'FILTER' => [
        ['id' => 'TITLE', 'name' => 'Название', 'type' => 'string', 'default' => true],
    ],
    'ENABLE_LABEL' => true,
    'ENABLE_LIVE_SEARCH' => true,
]);
?>

<?
$APPLICATION->IncludeComponent(
	'bitrix:main.ui.grid',
	'',
	$arResult["grid"],
	$component
);
?>