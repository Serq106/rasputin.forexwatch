<?php
namespace Rasputin\Forexwatch;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\TextField;

Loc::loadMessages(__FILE__);

/**
 * Class ForexwatchTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> CODE text optional
 * <li> DATE text optional
 * <li> COURSE text optional
 * </ul>
 *
 * @package Bitrix\Forexwatch
 **/

class ForexwatchTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'rasputin_forexwatch';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => Loc::getMessage('FOREXWATCH_ENTITY_ID_FIELD')
                ]
            ),
            new TextField(
                'CODE',
                [
                    'title' => Loc::getMessage('FOREXWATCH_ENTITY_CODE_FIELD')
                ]
            ),
            new TextField(
                'DATE',
                [
                    'title' => Loc::getMessage('FOREXWATCH_ENTITY_DATE_FIELD')
                ]
            ),
            new TextField(
                'COURSE',
                [
                    'title' => Loc::getMessage('FOREXWATCH_ENTITY_COURSE_FIELD')
                ]
            ),
        ];
    }
}