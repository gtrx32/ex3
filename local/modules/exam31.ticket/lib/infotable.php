<?php
namespace Exam31\Ticket;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

class InfoTable extends Entity\Datamanager
{
    static function getTableName(): string
    {
        return 'exam31_ticket_info';
    }

    public static function getMap(): array
    {
        return [
            (new Entity\IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),
            (new Entity\StringField('TITLE'))
                ->configureRequired()
                ->configureSize(250),
            (new Entity\IntegerField('ELEMENT_ID'))
                ->configureRequired(),
            new Entity\ReferenceField(
                'ELEMENT',
                SomeElementTable::class,
                ['=this.ELEMENT_ID' => 'ref.ID'],
                ['join_type' => 'INNER']
            ),
        ];
    }

    static function getFieldsDisplayLabel(): array
    {
        $fields = InfoTable::getMap();
        $res = [];
        foreach ($fields as $field)
        {
            $title = $field->getTitle();
            $res[$title] = Loc::getMessage("EXAM31_INFO_{$title}_FIELD_LABEL") ?? $title;
        }
        return $res;
    }
}