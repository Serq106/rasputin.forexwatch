<?
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/aimclo.logmail/admin/rasputin_forexwatch_edit.php")) {
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rasputin.forexwatch/admin/rasputin_forexwatch_edit.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/rasputin.forexwatch/admin/rasputin_forexwatch_edit.php");
}

