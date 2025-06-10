<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\SystemException;
use Bitrix\Main\UI\Extension;

use Exam31\Ticket\Entities\InfoTable;

class ExamElementsInfoComponent extends CBitrixComponent implements Controllerable, Errorable
{
    use \Bitrix\Main\ErrorableImplementation;
    private ?int $elementId = null;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->errorCollection = new ErrorCollection();
    }

    function onPrepareComponentParams($arParams)
    {
        if (!Loader::includeModule('exam31.ticket'))
        {
            $this->errorCollection->setError(
                new Error(Loc::getMessage('EXAM31_TICKET_MODULE_NOT_INSTALLED'))
            );
            return $arParams;
        }

        if (isset($arParams['ELEMENT_ID']))
        {
            $this->elementId = (int) $arParams['ELEMENT_ID'] ?: null;
        }

        return $arParams;
    }

    private function displayErrors(): void
    {
        foreach ($this->getErrors() as $error)
        {
            ShowError($error->getMessage());
        }
    }

    function executeComponent(): void
    {
        if ($this->hasErrors()) {
            $this->displayErrors();
            return;
        }

        $this->arResult['ITEMS'] = $this->getInfoItems();

        $this->includeComponentTemplate();

        global $APPLICATION;
        $APPLICATION->SetTitle(Loc::getMessage('EXAM31_INFO_PAGE_TITLE', ['#ID#' => $this->elementId]));
    }

    private function getInfoItems(): array
    {
        if (!$this->elementId) {
            return [];
        }

        $result = InfoTable::getList([
            'filter' => ['ELEMENT_ID' => $this->elementId],
            'order' => ['ID' => 'ASC']
        ]);

        return $result->fetchAll();
    }

    public function configureActions(): array
    {
        return [];
    }
}