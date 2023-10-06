<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('rasputin.forexwatch', [
    '\Rasputin\Forexwatch\ForexwatchTable' => 'lib/RasputinForexwatch.php',
]);
?>
