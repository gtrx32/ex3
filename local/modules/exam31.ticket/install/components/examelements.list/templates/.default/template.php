<?php B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

use \Bitrix\UI\Toolbar\Facade\Toolbar;
?>

<?
Toolbar::addFilter($arResult['filter']);
Toolbar::addButton($arResult['toolbar']['buttons'][0]);
?>

<?
$APPLICATION->IncludeComponent(
	'bitrix:main.ui.grid',
	'',
	$arResult["grid"],
	$component
);
?>