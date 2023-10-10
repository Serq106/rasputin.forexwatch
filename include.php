<?
use \Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
    'rasputin.forexwatch',
    array(
        'Rasputin\Forexwatch\ForexwatchTable' => 'lib/ForexwatchTable.php',
        'Rasputin\Forexwatch\ParserCurrency' => 'lib/ParserCurrency.php',
    )
);
?>
