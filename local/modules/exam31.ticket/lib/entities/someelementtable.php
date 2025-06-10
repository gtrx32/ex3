<?php
namespace Exam31\Ticket\Entities;

use Bitrix\Main\Entity;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;

class SomeElementTable extends Entity\DataManager
{
	static function getTableName(): string
	{
		return 'exam31_ticket_someelement';
	}

	static function getMap(): array
	{
        return [
            (new Entity\IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),
            (new Entity\DatetimeField('DATE_MODIFY'))
                ->configureRequired()
                ->configureDefaultValue(new DateTime()),
            (new Entity\BooleanField('ACTIVE'))
                ->configureRequired(),
            (new Entity\StringField('TITLE'))
                ->configureRequired()
                ->configureSize(250),
            new Entity\TextField('TEXT'),
            new Entity\ReferenceField(
                'INFO',
                InfoTable::class,
                ['=this.ID' => 'ref.ELEMENT_ID'],
                ['join_type' => 'LEFT']
            ),
            new Entity\ExpressionField(
                'INFO_COUNT',
                'COUNT(%s)',
                ['INFO.ID']
            ),
        ];
	}

	static function getFieldsDisplayLabel(): array
	{
		$fields = SomeElementTable::getMap();
		$res = [];
		foreach ($fields as $field)
		{
			$title = $field->getTitle();
			$res[$title] = Loc::getMessage("EXAM31_SOMEELEMENT_{$title}_FIELD_LABEL") ?? $title;
		}
		return $res;
	}
}