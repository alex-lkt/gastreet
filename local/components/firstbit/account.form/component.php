<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\CurrentUser;


Bitrix\Main\Loader::includeModule('iblock');
Bitrix\Main\Loader::includeModule("highloadblock");
Bitrix\Main\UI\Extension::load("ui.dialogs.messagebox");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$arUser['ID'] = CurrentUser::get()->getId();
$arUser['LOGIN'] = CurrentUser::get()->getLogin();
$arUser['EMAIL'] = CurrentUser::get()->getEmail();
$arPhone = \Bitrix\Main\UserPhoneAuthTable::getList([
    'filter' => ['=USER_ID' => $arUser['ID']],
    'select' => ['PHONE_NUMBER'],
])->fetch();
$arUser['PHONE_NUMBER'] = $arPhone['PHONE_NUMBER'];

//$iblockId = \Bitrix\Iblock\IblockTable::getList(['filter'=>['CODE'=>'users']])->Fetch()["ID"];
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


if ($_REQUEST['account_form']) {

}

//echo "<pre>: "; print_r($_REQUEST); echo "</pre>";


$this->IncludeComponentTemplate();
?>
