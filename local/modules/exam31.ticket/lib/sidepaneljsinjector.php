<?php
namespace Exam31\Ticket;

use \Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;

class SidePanelJsInjector
{
    public static function injectRules()
    {
        $labelText = Loc::getMessage('EXAM31_ELEMENTS_SIDEPANEL_LABEL');

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
                                            text: '{$labelText}'
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