<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("sotbit.yandex")!="D")
{
    $aMenu = array(
        "parent_menu" => "global_menu_content",
        "section" => "sotbit.yandex",
        "sort" => 10,
        "text" => GetMessage("mnu_sotbit_yandex_sect_title"),
        "title" => GetMessage("mnu_sotbit_yandex_sect_title"),
        "url" => "sotbit.yandex_api_list.php?lang=".LANGUAGE_ID,
        "icon" => "sotbit_yandex_menu_icon",
        "page_icon" => "sotbit_yandex_page_icon",
        "items_id" => "menu_sotbit.yandex",
        "items" => array(
            array(
                "text" => GetMessage("mnu_sotbit_yandex_api_list"),
                "url" => "sotbit.yandex_api_list.php?lang=".LANGUAGE_ID,
                "more_url" => array("sotbit.yandex_api_list.php", "sotbit.yandex_api_edit.php"),
                "title" => GetMessage("mnu_sotbit_yandex_api_list")
            ),
        )
    );

    return $aMenu;
}
return false;
?>