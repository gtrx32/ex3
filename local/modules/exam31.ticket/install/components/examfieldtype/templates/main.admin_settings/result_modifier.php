<?php

defined('B_PROLOG_INCLUDED') || die;

$values = [];
if (
	isset($arResult['additionalParameters']['bVarsFromForm'])
	&& $arResult['additionalParameters']['bVarsFromForm'])
{
    $values['FORMAT'] = $GLOBALS[$arResult['additionalParameters']['NAME']]['FORMAT'] ?? '';
    $values['URL_TEMPLATE'] = $GLOBALS[$arResult['additionalParameters']['NAME']]['URL_TEMPLATE'] ?? '';
}
elseif (isset($arResult['userField']) && $arResult['userField'])
{
    $values['FORMAT'] = $arResult['userField']['SETTINGS']['FORMAT'];
    $values['URL_TEMPLATE'] = $arResult['userField']['SETTINGS']['URL_TEMPLATE'];
}

$arResult['VALUES'] = $values;