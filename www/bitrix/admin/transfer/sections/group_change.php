<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
/**
 * Created by PhpStorm.
 * User: Asus-
 * Date: 15.07.2015
 * Time: 2:14
 */
CModule::IncludeModule('iblock');
define('LOG_FILENAME','group_change.log');
$section=new CIBlockSection;
if($USER->IsAdmin()){
    $arFields=array(
        'UF_SUBSECTIONS_VIEW'=>true
    );

    $dbSec=CIBlockSection::GetList(array(),array('IBLOCK_ID'=>1,'ELEMENT_SUBSECTIONS '=>'N'),true);
    while($sec=$dbSec->GetNext()){
        if($sec['ELEMENT_CNT']==0){
            $res=$section->Update($sec['ID'],$arFields);
            AddMessage2Log($sec[ID].' '.$sec['NAME'].' '.$res);
        }
       // AddMessage2Log($sec["ID"].' '.$sec['NAME'].' '.$sec['ELEMENT_CNT']);

    }

}
