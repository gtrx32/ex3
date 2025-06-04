<?php
namespace Exam31\Ticket;

use \Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;

class SidePanelJsInjector
{
    public static function injectRules()
    {
        $detailTitle = Loc::getMessage('EXAM31_ELEMENTS_SIDEPANEL_DETAIL_TITLE');
        $infoTitle = Loc::getMessage('EXAM31_ELEMENTS_SIDEPANEL_INFO_TITLE');
        $infoTitleBgColor = Loc::getMessage('EXAM31_ELEMENTS_SIDEPANEL_INFO_TITLE_BG_COLOR');

        $asset = Asset::getInstance();

        $asset->addString(
            $asset->insertJs(
                <<<JS
                    BX.ready(function (){
                        if (window.top !== window) {
                            return;
                        }
                        
                        BX.SidePanel.Instance.bindAnchors({
                            rules: [
                                {
                                    condition: [new RegExp('/exam31/detail/[0-9]+/')],
                                    options: {
                                        label: {
                                            text: '{$detailTitle}'
                                        }
                                    }
                                },
                                {
                                    condition: [new RegExp('/exam31/info/[0-9]+/')],
                                    options: {
                                        label: {
                                            text: '{$infoTitle}',
                                            bgColor: '{$infoTitleBgColor}'
                                        }
                                    }
                                }
                            ]
                        });
                    });
                JS,
                inline: true
            ),
        );
    }
}