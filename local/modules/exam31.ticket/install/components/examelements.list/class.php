<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Text\HtmlFilter;

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\ErrorableImplementation;

use Exam31\Ticket\SomeElementTable;
use Bitrix\Main\UI\Filter\Options;
use Bitrix\Main\UI\PageNavigation;

class ExamElementsListComponent extends CBitrixComponent implements Errorable
{
	use ErrorableImplementation;
	protected const DEFAULT_PAGE_SIZE = 20;
	protected const GRID_ID = 'EXAM31_GRID_ELEMENT';

	public function __construct($component = null)
	{
		parent::__construct($component);
		$this->errorCollection = new ErrorCollection();
	}

	public function onPrepareComponentParams($arParams): array
	{
		if (!Loader::includeModule('exam31.ticket'))
		{
			$this->errorCollection->setError(
				new Error(Loc::getMessage('EXAM31_TICKET_MODULE_NOT_INSTALLED'))
			);
			return $arParams;
		}

		$arParams['ELEMENT_COUNT'] = (int) $arParams['ELEMENT_COUNT'];
		if ($arParams['ELEMENT_COUNT'] <= 0)
		{
			$arParams['ELEMENT_COUNT'] = static::DEFAULT_PAGE_SIZE;
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

	public function executeComponent(): void
	{
		if ($this->hasErrors())
		{
			$this->displayErrors();
			return;
		}

        $filter = $this->getFilter();
        $navigation = $this->getNavigation();
        $items = $this->getSomeElementList($filter, $navigation);

		$this->arResult['ITEMS'] = $items;
		$this->arResult['grid'] = $this->prepareGrid($items, $navigation);

		$this->includeComponentTemplate();

		global $APPLICATION;
		$APPLICATION->SetTitle(Loc::getMessage('EXAM31_ELEMENTS_LIST_PAGE_TITLE'));
	}

    protected function getFilter(): array
    {
        $this->arResult['filterId'] = self::GRID_ID;

        $filterOptions = new Options(self::GRID_ID);

        return $filterOptions->getFilter([]);
    }

    protected function getNavigation(): PageNavigation
    {
        $navigation = new PageNavigation('n');

        $navigation->setPageSize(self::DEFAULT_PAGE_SIZE);
        $navigation->initFromUri();

        return $navigation;
    }

	protected function getSomeElementList(array $filter = [], PageNavigation $navigation = null): array
	{
        $queryFilter = [];

        if (!empty($filter['TITLE'])) {
            $queryFilter['%TITLE'] = $filter['TITLE'];
        }

        $params = $this->prepareParams($queryFilter, $navigation);

        $result = SomeElementTable::getList($params);

        $navigation->setRecordCount($result->getCount());

        $items = [];

        while ($item = $result->fetch())
        {
            $items[] = $this->prepareItem($item);
        }

        return $items;
	}

    protected function prepareParams(array $filter, PageNavigation $navigation): array
    {
        $params = [
            'filter' => $filter,
            'select' => ['ID', 'DATE_MODIFY', 'TITLE', 'TEXT', 'ACTIVE', 'INFO_COUNT'],
            'count_total' => true,
            'group' => ['ID', 'DATE_MODIFY', 'TITLE', 'TEXT', 'ACTIVE'],
            'order' => ['ID' => 'ASC'],
        ];

        $params['limit'] = $navigation->getLimit();
        $params['offset'] = $navigation->getOffset();

        return $params;
    }

    protected function prepareItem(array $item): array
    {
        return [
            'ID' => (int)$item['ID'],
            'ACTIVE' => $item['ACTIVE'] == 1
                ? Loc::getMessage('EXAM31_ELEMENTS_ACTIVE_VALUE_YES')
                : Loc::getMessage('EXAM31_ELEMENTS_ACTIVE_VALUE_NO'),
            'DATE_MODIFY' => $item['DATE_MODIFY'] instanceof DateTime
                ? $item['DATE_MODIFY']->toString()
                : null,
            'TITLE' => htmlspecialcharsbx($item['TITLE']),
            'TEXT' => htmlspecialcharsbx($item['TEXT']),
            'INFO_COUNT' => (int)($item['INFO_COUNT'] ?? 0),
            'DETAIL_URL' => $this->getDetailPageUrl($item['ID']),
            'INFO_URL' => $this->getInfoPageUrl($item['ID'])
        ];
    }

	protected function prepareGrid(array $items, PageNavigation $navigation): array
	{
		return [
			'GRID_ID' => static::GRID_ID,
			'COLUMNS' => $this->getGridColums(),
			'ROWS' => $this->getGridRows($items),
            'NAV_OBJECT' => $navigation,
            'TOTAL_ROWS_COUNT' => $navigation->getRecordCount(),
			'SHOW_ROW_CHECKBOXES' => false,
			'SHOW_SELECTED_COUNTER' => false,
            'SHOW_TOTAL_COUNTER' => true,
            'SHOW_PAGINATION' => true,
			'AJAX_MODE' => 'Y',
			'AJAX_OPTION_JUMP' => 'N',
			'AJAX_OPTION_HISTORY' => 'N',
		];
	}

	protected function getGridColums(): array
	{
		$fieldsLabel = SomeElementTable::getFieldsDisplayLabel();
		return [
			['id' => 'ACTIVE', 'default' => true, 'name' => $fieldsLabel['ACTIVE'] ?? 'ACTIVE'],
			['id' => 'ID', 'default' => true, 'name' => $fieldsLabel['ID'] ?? 'ID'],
			['id' => 'DATE_MODIFY', 'default' => true, 'name' => $fieldsLabel['DATE_MODIFY'] ?? 'DATE_MODIFY'],
			['id' => 'TITLE', 'default' => true, 'name' => $fieldsLabel['TITLE'] ?? 'TITLE'],
			['id' => 'TEXT', 'default' => true, 'name' => $fieldsLabel['TEXT'] ?? 'TEXT'],
			['id' => 'DETAIL', 'default' => true, 'name' => Loc::getMessage('EXAM31_ELEMENTS_LIST_GRID_COLUMN_DETAIL_NAME')],
            ['id' => 'INFO', 'default' => true, 'name' => Loc::getMessage('EXAM31_ELEMENTS_LIST_GRID_COLUMN_INFO_NAME')],
		];
	}
	protected function getGridRows(array $items): array
	{
		if (empty($items))
		{
			return [];
		}

		$rows = [];

		foreach ($items as $key => $item)
		{
			$rows[$key] = [
				'id' => $item["ID"],
				'columns' => [
					'ID' => $item["ID"],
					'DATE_MODIFY' => $item["DATE_MODIFY"],
					'TITLE' => $item["TITLE"],
					'TEXT' => $item["TEXT"],
					'ACTIVE' => $item["ACTIVE"],
					'DETAIL' => $this->getDetailHTMLLink($item["DETAIL_URL"]),
                    'INFO' => $this->getInfoHTMLLink($item["INFO_URL"], $item['INFO_COUNT']),
                ],
                'actions' => [
                    [
                        'TEXT' => Loc::getMessage('EXAM31_ELEMENTS_LIST_ACTION_DETAIL'),
                        'ONCLICK' => "window.location.href='".$item["DETAIL_URL"]."'",
                    ],
                    [
                        'TEXT' => Loc::getMessage('EXAM31_ELEMENTS_LIST_ACTION_INFO'),
                        'ONCLICK' => "window.location.href='".$item["INFO_URL"]."'",
                    ]
                ]
			];
		}

		return $rows;
	}

    protected function getDetailPageUrl(int $id): string
    {
        return str_replace('#ELEMENT_ID#', $id, $this->arParams['DETAIL_PAGE_URL']);
    }

    protected function getDetailHTMLLink(string $detail_url): string
    {
        return "<a href=\"" . $detail_url . "\">" . Loc::getMessage('EXAM31_ELEMENTS_LIST_GRID_COLUMN_DETAIL_NAME') . "</a>";
    }

    protected function getInfoPageUrl(int $id): string
    {
        return str_replace('#ELEMENT_ID#', $id, $this->arParams['INFO_PAGE_URL']);
    }

    protected function getInfoHTMLLink(string $info_url, int $info_count): string
    {
        return "<a href=\"" . $info_url . "\">" . Loc::getMessage('EXAM31_ELEMENTS_LIST_GRID_COLUMN_INFO_VALUE', ['#COUNT#' => $info_count]) . "</a>";
    }
}