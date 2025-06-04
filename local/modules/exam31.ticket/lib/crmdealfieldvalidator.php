<?php

namespace Exam31\Ticket;

use Bitrix\Main\Localization\Loc;
use Bitrix\Crm\DealTable;

class CrmDealFieldValidator
{
    public static function validateProtectedFieldChange(&$arFields)
    {
        if (!isset($arFields['ID'])) {
            return true;
        }

        $protectedFieldCode = 'UF_CRM_1749032750';

        if (!array_key_exists($protectedFieldCode, $arFields)) {
            return true;
        }

        $deal = DealTable::getList([
            'filter' => ['=ID' => $arFields['ID']],
            'select' => [$protectedFieldCode],
        ])->fetch();

        if (!$deal) {
            return true;
        }

        if ($deal[$protectedFieldCode] === $arFields[$protectedFieldCode]) {
            return true;
        }

        global $USER;

        $userGroups = $USER->GetUserGroupArray();
        $isAdmin = in_array(1, $userGroups);

        if (!$isAdmin) {
            $arFields['RESULT_MESSAGE'] = 'Вы не можете изменить значение поля «Защищенное поле»';
            return false;
        }

        return true;
    }
}