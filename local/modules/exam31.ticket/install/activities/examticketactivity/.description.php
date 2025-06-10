<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\FieldType;

$arActivityDescription = [
	"NAME" => Loc::getMessage("EXAM31_TICKET_ACTIVITY_DESCR_NAME"),
	"DESCRIPTION" => Loc::getMessage("EXAM31_TICKET_ACTIVITY_DESCR_DESCR"),
	"TYPE" => "activity",
	"CLASS" => "ExamTicketActivity",
	"JSCLASS" => "BizProcActivity",
	"CATEGORY" => [
		"ID" => "other",
	],
    "RETURN" => [
        "ID" => [
            "NAME" => Loc::getMessage("EXAM31_TICKET_ACTIVITY_RETURN_ID"),
            "TYPE" => FieldType::INT,
        ],
        "DATE_MODIFY" => [
            "NAME" => Loc::getMessage("EXAM31_TICKET_ACTIVITY_RETURN_DATE_MODIFY"),
            "TYPE" => FieldType::STRING,
        ],
        "ACTIVE" => [
            "NAME" => Loc::getMessage("EXAM31_TICKET_ACTIVITY_RETURN_ACTIVE"),
            "TYPE" => FieldType::STRING,
        ],
        "TITLE" => [
            "NAME" => Loc::getMessage("EXAM31_TICKET_ACTIVITY_RETURN_TITLE"),
            "TYPE" => FieldType::STRING,
        ],
        "TEXT" => [
            "NAME" => Loc::getMessage("EXAM31_TICKET_ACTIVITY_RETURN_TEXT"),
            "TYPE" => FieldType::STRING,
        ],
        "INFO_COUNT" => [
            "NAME" => Loc::getMessage("EXAM31_TICKET_ACTIVITY_RETURN_INFO_COUNT"),
            "TYPE" => FieldType::STRING,
        ],
    ],
];

?>