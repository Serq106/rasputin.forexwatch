<?
use Bitrix\Main\Loader;


############### первый общий пролог ########################
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/rasputin.forexwatch/admin/tools.php");

Loader::includeModule('rasputin.forexwatch');

$bxpublic = isset($_REQUEST['bxpublic']) && strlen($_REQUEST['bxpublic']) > 0;

# Проверка на доступ
$CURRENCY_RIGHT = $APPLICATION->GetGroupRight("rasputin.forexwatch");
if($CURRENCY_RIGHT == "D")
{
    $APPLICATION->AuthForm("Доступ запрещен");
}


# Создаем объект управления вкладками
$aTabs = [
    [
        "DIV" => "edit1",
        "TAB" => GetMessage("RASPUTIN_FOREXWATCH_TAB_MAIN"),
        "ICON"=>"main_user_edit",
        "TITLE"=>GetMessage("RASPUTIN_FOREXWATCH_TAB_MAIN")
    ],
];

$tabControl = new CAdminTabControl("tabControl", $aTabs);

# флаг "Данные получены с формы", обозначающий, что выводимые данные получены с формы, а не из БД.
$bVarsFromForm = false;

# Обработка данных
if($REQUEST_METHOD == "POST" && ($save != "" || $apply != "") && check_bitrix_sessid())
{
    $rawDate = $_REQUEST['DATE'];
    $dateTime = new \Bitrix\Main\Type\DateTime($rawDate, 'd.m.Y');

    $data = [
        'CODE' => $_REQUEST['CODE'],
        'DATE' => $dateTime,
        'COURSE' => $_REQUEST['COURSE'],
    ];

    if($ID > 0)
    {
        $result = Rasputin\Forexwatch\ForexwatchTable::update($ID, $data);
    }
    else
    {
        $result = Rasputin\Forexwatch\ForexwatchTable::add($data);
    }


    if($result->isSuccess())
    {
        if(!$bxpublic)
        {
            Rasputin\Forexwatch\AdminMessage::addMsg(($result instanceof Main\Entity\AddResult) ? 'Элемент добавлен':'Элемент обновлен');
        }
        if($save)
        {
            LocalRedirect("/bitrix/admin/rasputin_forexwatch_list.php");
        }
        else
        {
            LocalRedirect("/bitrix/admin/rasputin_forexwatch_edit.php?ID={$ID}");
        }
    }
    else
    {
        if(!$bxpublic)
        {
            Rasputin\Forexwatch\AdminMessage::addMsg(implode("<br>", $result->getErrorMessages()), 'false');
        }
        $bVarsFromForm = true;
    }
}

// ******************************************************************** //
//                ВЫБОРКА И ПОДГОТОВКА ДАННЫХ ФОРМЫ                     //
// ******************************************************************** //

# значения по умолчанию
$str_OLD_LINK = '';
$str_NEW_LINK = '';
$str_ACTIVE = 'Y';

# выборка данных
if($ID = intval($ID))
{
    $rsItem = new CDBResult(\Rasputin\Forexwatch\ForexwatchTable::getById($ID));
    if(!$rsItem->ExtractFields("str_"))
    {
        LocalRedirect("/bitrix/admin/");
    }
}

# если данные переданы из формы, инициализируем их
if($bVarsFromForm)
{
    $DB->InitTableVarsForEdit(\Rasputin\Forexwatch\ForexwatchTable::getTableName(), "", "str_");
}

$APPLICATION->SetTitle(($ID > 0 ? 'Редактирование элемента: '.$ID:'Добавление элемента'));

################# не забудем разделить подготовку данных и вывод ######################
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

# Административное меню
$aMenu = [
    [
        "TEXT"  => "Назад",
        "TITLE" => "Назад",
        "LINK"  => "/bitrix/admin/rasputin_forexwatch_list.php",
        "ICON"  => "btn_list",
    ]
];
if($ID > 0)
{
    $aMenu[] = ["SEPARATOR" => "Y"];
    $aMenu[] = [
        "TEXT"  => "Удалить",
        "TITLE" => "Удалить",
        "LINK"  => "javascript:if(confirm('Подтвердите удаление элемента'))window.location='rasputin_forexwatch_list.php?ID=".$ID."&action=delete&".bitrix_sessid_get()."';",
        "ICON"  => "btn_delete",
    ];
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

$res = \Rasputin\Forexwatch\ForexwatchTable::getById($_REQUEST['ID']);
$redirect_element = $res->fetch();

?>
<form method="POST" action="<?=$APPLICATION->GetCurPageParam()?>" ENCTYPE="multipart/form-data" name="post_form">
    <?=bitrix_sessid_post();?>
    <input type="hidden" name="ID" value="<?=$ID?>">
    <?
    # Вывод вкладок
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>

    <?
    $arMap = \Rasputin\Forexwatch\ForexwatchTable::getMap();

    foreach($arMap as $code => $field){
        ?>
        <tr>

            <td width="40%">
                <?if($field['required']):?>
                    <span class="adm-required-field"><?echo $field['title']?>:</span>
                <?else:?>
                    <?echo $field['title']?>:
                <?endif;?>
            </td>
            <td width="60%">
                <?if($field['editable']){?>
                    <?switch($field['data_type']){
                        case 'date':
                            echo CAdminCalendar::CalendarDate($code, $redirect_element[$code]->toString(), 19, true);
                            break;
                        case 'boolean':
                            ?><input type="checkbox" name="<?=$code?>" value="Y"<?if($redirect_element[$code] == "Y") echo " checked"?>/>	<?
                            break;
                        case 'string':
                            ?><textarea id="<?=$code?>" name="<?=$code?>" rows="5" cols="33"><?=$redirect_element[$code]?></textarea> <?
                            break;
                        case 'integer':
                        case 'float':
                        case 'text':
                            ?><input type="text" name="<?=$code?>" value="<?=$redirect_element[$code]?>" />	<?
                            break;
                    }?>
                <?}else{?>
                    <?if(is_object($redirect_element[$code])){
                        if(method_exists($redirect_element[$code],'toString')){
                            echo $redirect_element[$code]->toString();
                        }
                    }else{
                        echo $redirect_element[$code];
                        echo '<input type="hidden" name="ID" value="'.$redirect_element[$code].'" >';
                    }
                }?>
            </td>

        </tr>
    <?}?>

    <?

    # Вывод кнопок
    $tabControl->Buttons([
        'btnSave'   => true,
        'btnApply'  => true,
        'btnCancel' => true,
        'back_url'  => '/bitrix/admin/mhm.redirects_item_list.php',
    ]);

    # Конец вкладок
    $tabControl->End();
    ?>
</form>
<?

# информационная подсказка
echo BeginNote();
echo '<span class="required">*</span> '.GetMessage("REQUIRED_FIELDS");
echo EndNote();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
