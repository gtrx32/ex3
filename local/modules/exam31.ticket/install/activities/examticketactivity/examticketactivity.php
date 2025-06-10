<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Bizproc\Activity\PropertiesDialog;
use Bitrix\Main\Text\HtmlFilter;

class CBPExamTicketActivity extends BaseActivity
{
	public function __construct($name)
	{
		parent::__construct($name);

        $this->arProperties = [
            'ID' => 0,
            'DATE_MODIFY' => null,
            'ACTIVE' => null,
            'TITLE' => null,
            'TEXT' => null,
            'INFO_COUNT' => null
        ];

        $this->SetPropertiesTypes([
            'DATE_MODIFY' => ['Type' => FieldType::STRING],
            'ACTIVE' => ['Type' => FieldType::STRING],
            'TITLE' => ['Type' => FieldType::STRING],
            'TEXT' => ['Type' => FieldType::STRING],
            'INFO_COUNT' => ['Type' => FieldType::STRING]
        ]);
	}

	protected static function getFileName(): string
	{
		return __FILE__;
	}

	protected function internalExecute(): ErrorCollection
	{
		$errors = parent::internalExecute();

        $elementId = (int)$this->preparedProperties["ID"];

		if($elementId > 0)
		{
            $elementData = \Exam31\Ticket\SomeElementTable::getList([
                'filter' => ['ID' => $elementId],
                'select' => ['*', 'INFO_COUNT']
            ])->fetch();

            if ($elementData) {
                $this->preparedProperties['DATE_MODIFY'] = $elementData['DATE_MODIFY']
                    ? (string)$elementData['DATE_MODIFY']->format('d.m.Y H:i:s')
                    : '';
                $this->preparedProperties['ACTIVE'] = $elementData['ACTIVE'] == '1' ? 'Y' : 'N';
                $this->preparedProperties['TITLE'] = HtmlFilter::encode($elementData['TITLE']);
                $this->preparedProperties['TEXT'] = HtmlFilter::encode($elementData['TEXT']);
                $this->preparedProperties['INFO_COUNT'] = isset($elementData['INFO_COUNT'])
                    ? (string)$elementData['INFO_COUNT']
                    : '0';

                $this->log(
                    Loc::getMessage('EXAM31_TICKET_ACTIVITY_LOG_FOUND', ['#ID#' => $elementId])
                );
            }
            else {
                $this->preparedProperties['ID'] = 0;
                $this->preparedProperties['DATE_MODIFY'] = '';
                $this->preparedProperties['ACTIVE'] = '';
                $this->preparedProperties['TITLE'] = '';
                $this->preparedProperties['TEXT'] = '';
                $this->preparedProperties['INFO_COUNT'] = '';

                $this->log(
                    Loc::getMessage('EXAM31_TICKET_ACTIVITY_LOG_NOT_FOUND', ['#ID#' => $elementId])
                );
            }
		}
		else
		{
            $this->preparedProperties['ID'] = 0;
            $this->preparedProperties['DATE_MODIFY'] = '';
            $this->preparedProperties['ACTIVE'] = '';
            $this->preparedProperties['TITLE'] = '';
            $this->preparedProperties['TEXT'] = '';
            $this->preparedProperties['INFO_COUNT'] = '';

            $this->log(
                Loc::getMessage('EXAM31_TICKET_ACTIVITY_LOG_INVALID_ID', ['#ID#' => $elementId])
            );
		}

		return $errors;
	}

	public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
	{
        return [
            'ID' => [
                'Name' => 'ID',
                'FieldName' => 'ID',
                'Type' => FieldType::INT,
                'Required' => true,
                'Default' => '',
                'Options' => [],
            ],
        ];
	}
}