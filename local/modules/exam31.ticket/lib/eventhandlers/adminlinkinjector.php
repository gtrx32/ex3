<?php
namespace Exam31\Ticket\EventHandlers;

use Bitrix\Main\Localization\Loc;

class AdminLinkInjector
{
    public static function injectAdminLink(): void
    {
        global $USER, $APPLICATION;

        if ($USER->IsAuthorized() && in_array(1, $USER->GetUserGroupArray())) {
            $label = Loc::getMessage('EXAM31_TICKET_ADMIN_LINK_LABEL');
            $APPLICATION->AddViewContent("exam31_ticket_admin_link", '
                <div style="padding: 20px;">
                    <a href="/bitrix/admin/" class="ui-btn ui-btn-sm ui-btn-default">' . $label . '</a>
                </div>
            ');
        }
    }
}