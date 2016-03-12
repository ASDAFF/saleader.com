<?
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class SotbitYandexTable extends Entity\DataManager
{
    public static function getFilePath()
    {
       return __FILE__;
    }

   public static function getTableName()
   {
      return 'b_sotbit_yandex_list';
   }
   
   public static function getMap()
   {
      return array(
         'ID' => array(
            'data_type' => 'integer',
            'primary' => true,
            'autocomplete' => true,
            'title' => "ID",
         ),
         'NAME' => array(
            'data_type' => 'string',
            'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_name"),
         ),
         'ACTIVE' => array(
            'data_type' => 'boolean',
            'required' => true,
            'values' => array('N', 'Y'),
            'title' => Loc::getMessage("sotbit_yandex_entity_active"),
         ),
         'MODE' => array(
            'data_type' => 'string',
            'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_mode"),
         ),
         'TIMESTAMP_X' => array(
            'data_type' => 'datetime',
            //'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_datetime"),
         ),
         'IBLOCK_ID' => array(
            'data_type' => 'integer',
            'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_iblock_id"),
         ),
         'TASK' => array(
            'data_type' => 'string',
            'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_task"),
         ),
         /*'SECTION_ID' => array(
            'data_type' => 'integer',
            //'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_section_id"),
         ),*/
         'AGENT' => array(
            'data_type' => 'string',
            //'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_agent"),
         ),
         'AGENT_TIME' => array(
            'data_type' => 'integer',
            //'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_agent_time"),
         ),
         'START_LAST_TIME_X' => array(
            'data_type' => 'datetime',
            //'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_start_time"),
         ),
         'END_LAST_TIME_X' => array(
            'data_type' => 'datetime',
            //'required' => true,
            'title' => Loc::getMessage("sotbit_yandex_entity_end_time"),
         ),
         'SETTINGS' => array(
            'data_type' => 'string',
            //'required' => true,
            //'title' => Loc::getMessage("sotbit_yandex_entity_section_id"),
         ),
         
      );
   }
}
?>