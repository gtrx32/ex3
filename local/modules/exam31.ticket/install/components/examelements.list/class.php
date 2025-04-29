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
        $nav = $this->getNav();

        $items = $this->getSomeElementList($filter, $nav);

		$this->arResult['ITEMS'] = $items;
		$this->arResult['grid'] = $this->prepareGrid($items, $nav);

		$this->includeComponentTemplate();

		global $APPLICATION;
		$APPLICATION->SetTitle(Loc::getMessage('EXAM31_ELEMENTS_LIST_PAGE_TITLE'));
	}

    protected function getFilter(): array
    {
        $filterId = 'ELEMENTS_LIST_FILTER';

        $this->arResult['filterId'] = $filterId;

        $filterOptions = new Options($filterId);

        return $filterOptions->getFilter([]);
    }

    protected function getNav()
    {
        $nav = new PageNavigation('elements-list');

        $nav->allowAllRecords(false)
            ->setPageSize(20)
            ->initFromUri();

        return $nav;
    }

    protected function getSomeElementList(array $filter = [], PageNavigation $nav = null): array
    {
        $queryFilter = [];

        if (!empty($filter['TITLE'])) {
            $queryFilter['%TITLE'] = $filter['TITLE'];
        }

        $params = [
            'filter' => $queryFilter,
            'select' => ['ID', 'DATE_MODIFY', 'TITLE', 'TEXT', 'ACTIVE', 'INFO_COUNT'],
            'runtime' => [
                new \Bitrix\Main\Entity\ReferenceField(
                    'INFO',
                    \Exam31\Ticket\InfoTable::class,
                    ['=this.ID' => 'ref.ELEMENT_ID'],
                    ['join_type' => 'LEFT']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'INFO_COUNT',
                    'COUNT(%s)',
                    ['INFO.ID']
                )
            ],
            'count_total' => true,
            'group' => ['ID', 'DATE_MODIFY', 'TITLE', 'TEXT', 'ACTIVE'],
            'order' => ['ID' => 'ASC'],
        ];

        if ($nav) {
            $params['limit'] = $nav->getLimit();
            $params['offset'] = $nav->getOffset();
        }

        $result = SomeElementTable::getList($params);

        if ($nav) {
            $nav->setRecordCount($result->getCount());
        }

        $preparedItems = [];

        while ($item = $result->fetch()) {
            $item['ACTIVE'] = $item['ACTIVE'] == 1
                ? Loc::getMessage('EXAM31_ELEMENTS_ACTIVE_VALUE_YES')
                : Loc::getMessage('EXAM31_ELEMENTS_ACTIVE_VALUE_NO');

            $item['DATE_MODIFY'] = $item['DATE_MODIFY'] instanceof DateTime
                ? $item['DATE_MODIFY']->toString()
                : null;

            $item['TITLE'] = htmlspecialcharsbx($item['TITLE']);
            $item['TEXT'] = htmlspecialcharsbx($item['TEXT']);
            $item['DETAIL_URL'] = $this->getDetailPageUrl($item['ID']);
            $item['INFO_URL'] = $this->getInfoPageUrl($item['ID']);

            $preparedItems[] = $item;
        }

        return $preparedItems;
    }

    protected function prepareGrid(array $items, PageNavigation $nav): array
    {
        return [
            'GRID_ID' => static::GRID_ID,
            'COLUMNS' => $this->getGridColums(),
            'ROWS' => $this->getGridRows($items),
            'TOTAL_ROWS_COUNT' => $nav->getRecordCount(),
            'NAV_OBJECT' => $nav,
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
            ['id' => 'DETAIL', 'default' => true, 'name' => Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_DETAIL_NAME')],
            ['id' => 'INFO', 'default' => true, 'name' => Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_INFO_NAME')],
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
		return "<a href=\"" . $detail_url . "\">" . Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_DETAIL_NAME') . "</a>";
	}

    protected function getInfoPageUrl(int $id): string
    {
        return str_replace('#ELEMENT_ID#', $id, $this->arParams['INFO_PAGE_URL']);
    }

    protected function getInfoHTMLLink(string $info_url, int $info_count): string
    {
        return "<a href=\"" . $info_url . "\">" . Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_INFO_VALUE', ['#COUNT#' => $info_count]) . "</a>";
    }
}