<?

IncludeModuleLangFile(__FILE__); // в menu.php точно так же можно использовать языковые файлы

if ($APPLICATION->GetGroupRight("rasputin.forexwatch") >= "R") { // проверка уровня доступа к модулю

    $aMenu = [
        "parent_menu" => "global_menu_services",
        "sort" => 100,
        "text" => GetMessage("RASPUTIN_FOREXWATCH_MENU_TEXT"),
        "title"=> GetMessage("RASPUTIN_FOREXWATCH_MENU_TITLE"),
        "icon" => "highloadblock_menu_icon",
        "page_icon" => "highloadblock_page_icon",
        "items_id" => "menu_ben",
        "url"=>"/bitrix/admin/settings.php?lang=ru&mid=rasputin.forexwatch",
    ];

    // вернем полученный список
    return $aMenu;
}
// если нет доступа, вернем false
return false;
