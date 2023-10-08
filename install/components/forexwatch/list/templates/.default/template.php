<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (empty($arResult['ITEMS'])) {
    echo "Данные не найдены";
} else {
    echo "<table>";
    echo "<tr><th>ID</th><th>CODE</th><th>DATE</th><th>COURSE</th></tr>";
    foreach ($arResult['ITEMS'] as $item) {
        echo "<tr>";
        echo "<td>".$item['ID']."</td>";
        echo "<td>".$item['CODE']."</td>";
        echo "<td>".$item['DATE']."</td>";
        echo "<td>".$item['COURSE']."</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Вывод пагинации
    $APPLICATION->IncludeComponent(
        "bitrix:main.pagenavigation",
        "",
        array(
            "NAV_OBJECT" => $arResult['NAV_OBJECT'],
            "SEF_MODE" => "N",
        ),
        false
    );
}