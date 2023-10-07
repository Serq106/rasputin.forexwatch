<?
    use Bitrix\Main\Loader;

    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/rasputin.forexwatch/admin/tools.php");

    Loader::includeModule('rasputin.forexwatch');

    $listTableId = "tbl_rasputin_forexwatch_list";

    $oSort = new CAdminSorting($listTableId, "ID", "asc");
    $arOrder = (strtoupper($by) === "ID" ? [$by => $order] : [$by => $order, "ID" => "ASC"]);

    $adminList = new CAdminList($listTableId, $oSort);

    function CheckFilter(){
        global $arFilterFields, $adminList;
        foreach ($arFilterFields as $f) global $$f;

        // В данном случае проверять нечего.
        // В общем случае нужно проверять значения переменных $find_имя
        // и в случае возниконовения ошибки передавать ее обработчику
        // посредством $adminList->AddFilterError('текст_ошибки').

        return count($adminList->arFilterErrors)==0; // если ошибки есть, вернем false;
    }
    // опишем элементы фильтра
    $arFilterFields = [
        "find_active",
    ];
    // инициализируем фильтр
    $adminList->InitFilter($arFilterFields);
    // если все значения фильтра корректны, обработаем его
    if (CheckFilter()){
        $arFilter = [];

        if (!empty($find_active)){
            $arFilter["ACTIVE"] = $find_active;
        }
    }


    // сохранение отредактированных элементов
    if($adminList->EditAction()){
        // пройдем по списку переданных элементов
        foreach($FIELDS as $ID=>$arFields){

            if(!$adminList->IsUpdated($ID))
                continue;

            // сохраним изменения каждого элемента
            $DB->StartTransaction();
            $ID = IntVal($ID);
            $res = \Rasputin\Forexwatch\ForexwatchTable::getById($ID);
            if(!$arData = $res->fetch()){
                foreach($arFields as $key=>$value){
                    $arData[$key]=$value;
                }
                $result = \Rasputin\Forexwatch\ForexwatchTable::update($ID, $arData);

                if(!$result->isSuccess()){
                    if($e = $result->getErrorMessages()){
                        $adminList->AddGroupError(GetMessage("RASPUTIN_FOREXWATCH_SAVE_ERROR")." ".$e, $ID);
                    }
                    $DB->Rollback();
                }
            }else{
                $adminList->AddGroupError(GetMessage("RASPUTIN_FOREXWATCH_SAVE_ERROR")." ".GetMessage("RASPUTIN_FOREXWATCH_NO_ELEMENT"), $ID);
                $DB->Rollback();
            }
            $DB->Commit();
        }
    }
    // обработка одиночных и групповых действий
    if(($arID = $adminList->GroupAction())){
        // если выбрано "Для всех элементов"
        if($_REQUEST['action_target']=='selected'){
            $rsData = \Rasputin\Forexwatch\ForexwatchTable::getList(
                [
                    "filter" => $arFilter,
                    'order' => [$by=>$order]
                ]
            );
            while($arRes = $rsData->fetch())
                $arID[] = $arRes['ID'];
        }

        // пройдем по списку элементов
        foreach($arID as $ID){
            if(strlen($ID) <= 0)
                continue;
            $ID = IntVal($ID);

            // для каждого элемента совершим требуемое действие
            switch($_REQUEST['action']){
                // удаление
                case "delete":
                    @set_time_limit(0);
                    $DB->StartTransaction();
                    $result = \Rasputin\Forexwatch\ForexwatchTable::delete($ID);
                    if(!$result->isSuccess())
                    {
                        $DB->Rollback();
                        $adminList->AddGroupError(GetMessage("RASPUTIN_FOREXWATCH_DELETE_ERROR"), $ID);
                    }
                    $DB->Commit();
                    break;

                // активация/деактивация
                case "activate":
                case "deactivate":

                    if(($rsData = \Rasputin\Forexwatch\ForexwatchTable::getById($ID)) && ($arFields = $rsData->fetch()))
                    {
                        $arFields["ACTIVE"]=($_REQUEST['action']=="activate"?"Y":"N");
                        $result = \Rasputin\Forexwatch\ForexwatchTable::update($ID, $arFields);
                        if(!$result->isSuccess())
                            if($e = $result->getErrorMessages())
                                $adminList->AddGroupError(GetMessage("RASPUTIN_FOREXWATCH_SAVE_ERROR").$e, $ID);
                    }
                    else
                        $adminList->AddGroupError(GetMessage("RASPUTIN_FOREXWATCH_SAVE_ERROR")." ".GetMessage("RASPUTIN_FOREXWATCH_NO_ELEMENT"), $ID);
                    break;
            }
        }
    }

    $myData = \Rasputin\Forexwatch\ForexwatchTable::getList(
        [
            'filter' => $arFilter,
            'order' => $arOrder
        ]
    );

    $myData = new CAdminResult($myData, $listTableId);
    $myData->NavStart();

    $adminList->NavText($myData->GetNavPrint(GetMessage("RASPUTIN_FOREXWATCH_ADMIN_NAV")));
    $cols = \Rasputin\Forexwatch\ForexwatchTable::getMap();


    $colHeaders = [];

    foreach ($cols as $colId => $col)
    {

        $colHeaders[] = [
            "id" => $colId,
            "content" => $col["title"],
            "sort" => $colId,
            "default" => true,
        ];
    }

    $adminList->AddHeaders($colHeaders);

    $visibleHeaderColumns = $adminList->GetVisibleHeaderColumns();
    $arUsersCache = [];
    $arElementCache = [];

    while ($arRes = $myData->GetNext()){

        $row =& $adminList->AddRow($arRes["ID"], $arRes);

        if (in_array("ACTIVE", $visibleHeaderColumns)){
            $row->AddViewField("ACTIVE", $arRes['ACTIVE'] == 'Y'?GetMessage("RASPUTIN_FOREXWATCH_YES"):GetMessage("RASPUTIN_FOREXWATCH_NO"));
        }


        $el_edit_url = htmlspecialcharsbx(\Rasputin\Forexwatch\Tools::GetAdminElementEditLink($arRes["ID"]));
        $arActions = [];
        $arActions[] = ["ICON" => "edit", "TEXT" => GetMessage("RASPUTIN_FOREXWATCH_EDIT"), "ACTION" => $adminList->ActionRedirect($el_edit_url), "DEFAULT" => true,];
        $arActions[] = ["ICON" => "delete", "TEXT" => GetMessage("RASPUTIN_FOREXWATCH_DELETE"), "ACTION" => "if(confirm('" . GetMessageJS("RASPUTIN_FOREXWATCH_DEL_CONF") . "')) " . $adminList->ActionDoGroup($arRes["ID"], "delete"),];
        $row->AddActions($arActions);
    }

    $adminList->AddFooter(
        [
            [
                "title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
                "value" => $myData->SelectedRowsCount()
            ],
            [
                "counter" => true,
                "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
                "value" => "0"
            ],
        ]
    );

    // групповые действия
    $adminList->AddGroupActionTable(Array(
        "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"), // удалить выбранные элементы
        "activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"), // активировать выбранные элементы
        "deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"), // деактивировать выбранные элементы
    ));

    $adminList->CheckListMode();

    $APPLICATION->SetTitle(GetMessage("RASPUTIN_FOREXWATCH_ADMIN_TITLE"));

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

    $adminList->DisplayList();

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>