<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Component\BaseUfComponent;
use Bitrix\Main\Loader;
use Bitrix\Main\Text\HtmlFilter;
use Exam31\Ticket\FieldTypes\ExamFieldType;
use Exam31\Ticket\Entities\SomeElementTable;

class SomeElementFieldComponent extends BaseUfComponent
{
	public function __construct($component = null)
	{
		Loader::requireModule('exam31.ticket');
		parent::__construct($component);
	}

	protected static function getUserTypeId(): string
	{
		return ExamFieldType::USER_TYPE_ID;
	}

	public function prepareValue($value)
	{
        $id = (int) $value;

        $preparedValue = [
            'VALUE' => $id,
        ];

        $element = $this->getElementById($id);
        $elementFound = !empty($element);

        $title = $element['TITLE'] ?? '';
        $formatted = $this->buildFormattedValue($id, $title, $elementFound);

        $preparedValue['FORMATTED_VALUE'] = $formatted;

        $preparedValue['LINK'] = ($elementFound)
            ? $this->buildLink($id)
            : null;

        return $preparedValue;
	}

    protected function getElementById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $element = SomeElementTable::getByPrimary($id)->fetch();

        return (!empty($element['TITLE'])) ? $element : null;
    }

    protected function buildFormattedValue(int $id, string $title, bool $elementFound): string
    {
        $format = (string) ($this->arResult['userField']['SETTINGS']['FORMAT'] ?? '#ID#');

        $formatted = HtmlFilter::encode($format);

        $formatted = str_replace(
            ['#ID#', '#TITLE#'],
            [$id, $title],
            $formatted
        );

        if (!$elementFound) {
            $formatted .= ' Элемент не найден';
        }

        return $formatted;
    }

    protected function buildLink(int $id): ?string
    {
        $urlTemplate = (string) ($this->arResult['userField']['SETTINGS']['URL_TEMPLATE'] ?? '');

        return !empty($urlTemplate)
            ? str_replace('#ID#', $id, $urlTemplate)
            : null;
    }
}