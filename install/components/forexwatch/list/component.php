<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\UI\PageNavigation;

if (!Loader::includeModule("rasputin.forexwatch")) {
    ShowError("Модуль rasputin.forexwatch не установлен");
    return;
}

$filter = array();

if (!empty($_REQUEST["DATE_min"])) {
    $filter[">=DATE"] = $_REQUEST["DATE_min"];
}
if (!empty($_REQUEST["DATE_max"])) {
    $filter["<=DATE"] = $_REQUEST["DATE_max"];
}

if (!empty($_REQUEST["COURSE_min"])) {
    $filter[">=COURSE"] = $_REQUEST["COURSE_min"];
}
if (!empty($_REQUEST["COURSE_max"])) {
    $filter["<=COURSE"] = $_REQUEST["COURSE_max"];
}

// Настройка пагинации
$nav = new PageNavigation("nav");
$nav->allowAllRecords(true)
    ->setPageSize(10) // Количество элементов на странице
    ->initFromUri(); // Инициализация из URL

$arResult['NAV_OBJECT'] = $nav;

// Выполнение запроса к базе данных с учетом пагинации
$res = Rasputin\Forexwatch\ForexwatchTable::getList(array(
    'select' => array('*'),
    'filter' => $filter,
    'count_total' => true,
    'offset' => $nav->getOffset(),
    'limit' => $nav->getLimit(),
));

$nav->setRecordCount($res->getCount()); // Установка общего количества элементов для пагинации

while ($row = $res->fetch()) {
    $arResult['ITEMS'][] = $row;
}
$this->IncludeComponentTemplate();