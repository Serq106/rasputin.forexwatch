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

        $DB->RunSQLBatch(dirname(__FILE__)."/sql/install.sql");

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB;
        UnRegisterModule(self::MODULE_ID);

        $DB->RunSQLBatch(dirname(__FILE__)."/sql/uninstall.sql");

        return true;
    }

    function InstallEvents()
    {
        CAgent::AddAgent("Rasputin\Forexwatch\ParserCurrency::agentLaunchingParser();","rasputin.forexwatch", "N", 86400, "", "Y", "", 10);
        return true;
    }

    function UnInstallEvents()
    {
        CAgent::RemoveAgent("Rasputin\Forexwatch\ParserCurrency::agentLaunchingParser();", "rasputin.forexwatch");
        return true;
    }

    function InstallFiles($arParams = array())
    {
        CopyDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
        CopyDirFiles(__DIR__ . '/components/forexwatch',$_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/forexwatch',true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        DeleteDirFilesEx('/bitrix/components/forexwatch');
        return true;
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB();
        $this->InstallEvents();
    }

    function DoUninstall()
    {
        $this->UnInstallEvents();
        $this->UnInstallDB();
        $this->UnInstallFiles();
    }
}
?>
