<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filter = array();

    if (!empty($_REQUEST["DATE_min"])) {
        $filter["DATE_min"] = date('d.m.Y', strtotime($_REQUEST["DATE_min"]));
    }
    if (!empty($_REQUEST["DATE_max"])) {
        $filter["DATE_max"] = date('d.m.Y', strtotime($_REQUEST["DATE_max"]));
    }

    if (!empty($_REQUEST["COURSE_min"])) {
        $filter["COURSE_min"] = $_REQUEST["COURSE_min"];
    }
    if (!empty($_REQUEST["COURSE_max"])) {
        $filter["COURSE_max"] = $_REQUEST["COURSE_max"];
    }


    $currentUrl = $APPLICATION->GetCurPage(false).'?'.http_build_query($filter);


    LocalRedirect($currentUrl);
} else {
    // Инициализация фильтра по умолчанию
    $arResult["FILTER"] = array(
        "DATE" => "",
        "CODE" => "",
        "COURSE" => "",
        // Добавьте другие поля фильтрации по аналогии
    );
}

$this->IncludeComponentTemplate();