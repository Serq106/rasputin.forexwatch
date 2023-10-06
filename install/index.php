<?
IncludeModuleLangFile(__FILE__);
if (class_exists("rasputin_forexwatch"))
    return;

Class rasputin_forexwatch extends CModule
{
    const MODULE_ID = 'rasputin.forexwatch';
    var $MODULE_ID = 'rasputin.forexwatch';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__)."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("RASPUTIN_FOREXWATCH_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("RASPUTIN_FOREXWATCH_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("RASPUTIN_FOREXWATCH_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("RASPUTIN_FOREXWATCH_PARTNER_URI");
    }

    function InstallDB($arParams = array())
    {
        global $DB;
        RegisterModule(self::MODULE_ID);
        /**
         * Создание глобального меню
         */
        RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CHORedirect', 'OnBuildGlobalMenu');

        require_once realpath(__DIR__.'/../include.php');

        CAgent::AddAgent("Rasputin\Forexwatch\ParserCurrency::agentLaunchingParser();","rasputin.forexwatch", "N", 86400, "", "Y", "", 10);


        /**
         * Установка таблицы
         */
        $DB->RunSQLBatch(dirname(__FILE__)."/sql/install.sql");

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB;
        UnRegisterModule(self::MODULE_ID);
        UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CHOredirect', 'OnBuildGlobalMenu');

        require_once realpath(__DIR__.'/../include.php');

        CAgent::RemoveAgent("Rasputin\Forexwatch\ParserCurrency::agentLaunchingParser();", "rasputin.forexwatch");


        $DB->RunSQLBatch(dirname(__FILE__)."/sql/uninstall.sql");

        return true;
    }

    function InstallEvents()
    {

        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallFiles($arParams = array())
    {
        CopyDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        return true;
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallEvents();
        $this->InstallDB();
    }

    function DoUninstall()
    {
        global $APPLICATION;

        $this->UnInstallEvents();
        $this->UnInstallDB();
        $this->UnInstallFiles();
    }
}
?>
