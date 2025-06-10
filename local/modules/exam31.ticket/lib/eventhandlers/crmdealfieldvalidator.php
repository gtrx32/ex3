<?php

namespace Exam31\Ticket;

use Bitrix\Main\Localization\Loc;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\Service\Container;

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

        $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
        $deal = $dealFactory->getItem($arFields['ID']);

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
            $arFields['RESULT_MESSAGE'] = Loc::getMessage('EXAM31_TICKET_CANCEL_UPDATE_PROTECTED_FIELD');
            return false;
        }

        return true;
    }
}