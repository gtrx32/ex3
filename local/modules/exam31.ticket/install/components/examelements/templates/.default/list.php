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
	'exam31.ticket:examelements.list',
	'.default',
	[
		'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'] ?? null,
		'DETAIL_PAGE_URL' => $arResult['DETAIL_PAGE_URL'],
		'LIST_PAGE_URL' => $arResult['LIST_PAGE_URL'],
        'INFO_PAGE_URL' => $arResult['INFO_PAGE_URL'],
	]
);