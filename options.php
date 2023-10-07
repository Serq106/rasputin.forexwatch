<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'rasputin.forexwatch');

global $USER, $APPLICATION;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages($context->getServer()->getDocumentRoot() . "/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);
if(!Bitrix\Main\Loader::includeModule("iblock") || !Bitrix\Main\Loader::includeModule(ADMIN_MODULE_NAME)){
    CAdminMessage::showMessage([
        "MESSAGE" => Loc::getMessage("RASPUTIN_FOREXWATCH_ERROR_MODULE"),
        "TYPE" => "ERROR",
    ]);
    return;
}?>
