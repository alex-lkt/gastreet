<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\CurrentUser;

Bitrix\Main\Loader::includeModule('iblock');
Bitrix\Main\Loader::includeModule("highloadblock");
Bitrix\Main\UI\Extension::load("ui.dialogs.messagebox");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

global $USER;

if ($_REQUEST['account_form']) {
    $result = ['status' => false, 'mess' => ''];
    $phone = \firstbit\SmsHelpers::phone_format($_REQUEST['PHONE_NUMBER'], '#');

    $user = new CUser;
    $userID = $USER->GetID();
    if ($userID > 0) {
        // массив параметров для обновления пользователя
        $fields = array(
            "NAME" => htmlspecialcharsbx($_REQUEST['FIRST_NAME']),
            "LAST_NAME" => htmlspecialcharsbx($_REQUEST['LAST_NAME']),
            "EMAIL" => htmlspecialcharsbx($_REQUEST['EMAIL']),
            "LOGIN" => \firstbit\SmsHelpers::checkCodePhone($phone).$phone,
            "PHONE_NUMBER" => \firstbit\SmsHelpers::checkCodePhone($phone).$phone,
        );
        if ($user->Update($userID, $fields)) {
            $result['status'] = true;
            $result['mess'] = "Информация о пользователе успешно обновлена";
        } else {
            $result['mess'] =  $user->LAST_ERROR;
        }

        // данные для обновления Основных свойств ИБ
        $iblockId = \Bitrix\Iblock\IblockTable::getList(['filter'=>['CODE'=>'users']])->Fetch()["ID"];
        $arFilter = ["IBLOCK_ID" => $iblockId, "PROPERTY_USER_ID" => $userID];
        $res = CIBlockElement::GetList([], $arFilter, false, false, []);
        while($arFields = $res->GetNext())
        {
            $elId = $arFields['ID'];
        }
        if ($elId) {
            $el = new CIBlockElement;
            $arLoadProductArray = Array(
                "MODIFIED_BY"    => $userID,
                "IBLOCK_ID"      => $iblockId,
                "MODIFIED_BY"    => $userID,
                "NAME"           => htmlspecialcharsbx($_REQUEST['LAST_NAME']),
            );
            $res = $el->Update($elId, $arLoadProductArray);

            // данные для обновления свойств ИБ
            if (!empty($_REQUEST['POSITION2'])) {
                $position = htmlspecialcharsbx($_REQUEST['POSITION2']);
            } else {
                $position = htmlspecialcharsbx($_REQUEST['POSITION']);
            }
            CIBlockElement::SetPropertyValuesEx(
                $elId,
                $iblockId,
                [
                    'LAST_NAME' => htmlspecialcharsbx($_REQUEST['LAST_NAME']),
                    'FIRST_NAME' => htmlspecialcharsbx($_REQUEST['FIRST_NAME']),
                    'PATRONYMIC' => htmlspecialcharsbx($_REQUEST['PATRONYMIC']),
                    'COUNTRY' => htmlspecialcharsbx($_REQUEST['COUNTRY']),
                    'CITY' => htmlspecialcharsbx($_REQUEST['CITY']),
                    'INN' => htmlspecialcharsbx($_REQUEST['INN']),
                    'COMPANY_NAME' => htmlspecialcharsbx($_REQUEST['COMPANY_NAME']),
                    'COMPANY_ADDRESS' => htmlspecialcharsbx($_REQUEST['COMPANY_ADDRESS']),
                    'POSITION' => $position,
                    'COMPANY_BRAND' => htmlspecialcharsbx($_REQUEST['COMPANY_BRAND']),
                ]
            );
        }


    }
}
//echo "<pre>: "; print_r($_REQUEST); echo "</pre>";

$arUser['ID'] = CurrentUser::get()->getId();
$arUser['LOGIN'] = CurrentUser::get()->getLogin();
$arUser['EMAIL'] = CurrentUser::get()->getEmail();
$arPhone = \Bitrix\Main\UserPhoneAuthTable::getList([
    'filter' => ['=USER_ID' => $arUser['ID']],
    'select' => ['PHONE_NUMBER'],
])->fetch();
$arUser['PHONE_NUMBER'] = $arPhone['PHONE_NUMBER'];

$entityClass = "\Bitrix\Iblock\Elements\ElementUsersTable";
$arElement = $entityClass::getList([
    'select' => [
        'ID',
        'NAME',
        'USER_ID_' => 'USER_ID',
        'LAST_NAME_' => 'LAST_NAME',
        'FIRST_NAME_' => 'FIRST_NAME',
        'PATRONYMIC_' => 'PATRONYMIC',
        'CITY_' => 'CITY',
        'INN_' => 'INN',
        'COMPANY_NAME_' => 'COMPANY_NAME',
        'COMPANY_ADDRESS_' => 'COMPANY_ADDRESS',
        'POSITION_' => 'POSITION',
        'COMPANY_BRAND_' => 'COMPANY_BRAND',
    ],
    'filter' => [
        'USER_ID_VALUE' => $arUser['ID'],
    ],
])->fetch();
$arResult['ITEMS'] = array_merge($arUser, $arElement);

// Список стран и Список должностей
$hlbl = ['2' => 'UF_COUNTRY_NAME', '3' => 'UF_POSITION_NAME'];
foreach ($hlbl as $key => $item) {
    $hlblock = HL\HighloadBlockTable::getById($key)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $rsData = $entity_data_class::getList(array(
        "select" => [$item],
        "order" => ["UF_SORT" => "ASC"],
    ));

    while($arData = $rsData->Fetch()){
        $arResult['ITEMS'][$item][] = $arData[$item];
    }
}

//echo "<pre>: "; print_r($arResult['ITEMS']['POSITION_VALUE']); echo "</pre>";

$this->IncludeComponentTemplate();
?>
