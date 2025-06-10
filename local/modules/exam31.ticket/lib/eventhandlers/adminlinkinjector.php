<?php
namespace Exam31\Ticket\EventHandlers;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use \Bitrix\Main\Page\Asset;

class AdminLinkInjector
{
    public static function injectAdminLink(): void
    {
        if (Context::getCurrent()->getRequest()->isAdminSection()) {
            return;
        }

        global $USER;

        if ($USER->IsAuthorized() && in_array(1, $USER->GetUserGroupArray()))
        {
            $label = Loc::getMessage('EXAM31_TICKET_ADMIN_LINK_LABEL');

            $asset = Asset::getInstance();

            $asset->addString(
                $asset->insertJs(
                    <<<JS
                        BX.ready(function () {
                            if (document.getElementById('adminPanelLink')) {
                                return;
                            }
                            
                            const extraBtnBox = document.querySelector('.menu-extra-btn-box');
                            if (extraBtnBox === null) {
                                return;
                            }
                            
                            const wrapperDiv = document.createElement('div');
                            wrapperDiv.style.padding = '20px';
                            
                            const adminPanelLink = document.createElement('a');
                            adminPanelLink.id = 'adminPanelLink';
                            adminPanelLink.href = '/bitrix/admin/';
                            adminPanelLink.innerText = '{$label}';
                            adminPanelLink.classList.add('ui-btn', 'ui-btn-sm', 'ui-btn-default');
                            
                            wrapperDiv.appendChild(adminPanelLink);
                            extraBtnBox.parentNode.insertBefore(wrapperDiv, extraBtnBox);
                        });
                    JS,
                    inline: true
                ),
            );
        }
    }
}