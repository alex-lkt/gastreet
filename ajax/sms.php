<?php
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

session_start();
ini_set('max_execution_time', 20000);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once ($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/lib/sms/sms.ru.php");

use Bitrix\Main\UserTable;
use Bitrix\Main\UserPhoneAuthTable;
use Bitrix\Main\UserFieldTable;
use Bitrix\Main\Type\DateTime;

global $USER;

$data = json_decode(file_get_contents('php://input'), true);
$phone = \firstbit\SmsHelpers::phone_format($data['number_login'], '#');
$result['res'] = "no";

file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "1.0.0. ".date("Y-m-d H:i:s")." data: " . print_r([$data, $phone], 1) . PHP_EOL, FILE_APPEND);

// test
/*$result['phone'] = $phone;
echo json_encode($result);
die;*/
// end test

switch ($data['action']) {
    case "get_code":
        // Проверка телефона, если есть такой, генерируем код, записываем в UF свойство
        $rsUsers = getUser(null, $phone, null);

        if(isset($rsUsers['ID'])){
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "1.0.2. ".date("Y-m-d H:i:s")." user_id: " . print_r($rsUsers, 1) . PHP_EOL, FILE_APPEND);

            // если найден записываем его id в сессию по коду из смс
            $rand = rand(100000, 999999);
            $_SESSION['rand_sms'] = $rand;
            $_SESSION['USER_ID_AUTH'][$_SESSION['rand_sms']] = $rsUsers['ID'];
            // отправляем смс
            $result = sendSms((string) $phone, $rand);
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "1.0.3. ".date("Y-m-d H:i:s")." resSend: " . print_r([$rand, $result], 1) . PHP_EOL, FILE_APPEND);
        }
        else{
            // Пользователь не найден, заводим нового
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "1.0.7. ".date("Y-m-d H:i:s")." user_id: NO | Пользователь не найден, переходим к созданию нового..." . PHP_EOL, FILE_APPEND);

            $rand = rand(100000, 999999);
            $result = sendSms((string) $phone, $rand);

            $result['res'] = 'no-user';
        }

        echo json_encode($result);
        break;
    case "code_auth":

        if(isset($data['code_auth'])){
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.0.0. ".date("Y-m-d H:i:s")." code_auth: " . print_r([$_SESSION['USER_ID_AUTH'], $data, $phone], 1) . PHP_EOL, FILE_APPEND);

            if(isset($_SESSION['USER_ID_AUTH'][$data['code_auth']])){
                // Отправлен код
                $auth = $USER->Authorize($_SESSION['USER_ID_AUTH'][$data['code_auth']]);
                if ($auth) {
                    $result['res'] = 'OK';
                    $result['link'] = OK_AUTH_LINK;

                    // Новый код, обновляем у пользователя UF_SMS_CODE
                    $fields = [
                        "UF_SMS_CODE" => $data['code_auth'],
                        "UF_CODE_TIME" => date("d.m.Y H:i:s")
                    ];
                    $userUpdate = $USER->Update($USER->GetID(), $fields);
                    if ($userUpdate === false) {
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.0.6. ".date("Y-m-d H:i:s")." Update UF_SMS_CODE: ERROR " . print_r($userUpdate, 1) . PHP_EOL, FILE_APPEND);
                    } else {
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.0.1. ".date("Y-m-d H:i:s")." Authorize: OK, Update UF_SMS_CODE: OK | status" . print_r($userUpdate, 1)  . PHP_EOL, FILE_APPEND);
                    }

                } else {
                    $result['res'] = 'NO';
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.0.5. ".date("Y-m-d H:i:s")." Authorize ERROR (нет авторизации по новому коду), status: " . print_r($auth, 1) . PHP_EOL, FILE_APPEND);
                }

            } else {
                // В сессии нет записи о генерации нового кода - или введен старый код и нажата была кнопка Вход или новый пользователь
                if (!empty($phone)) {
                    // Проверка телефона, если есть такой, проверяем код
                    $rsUsers = getUser(null, $phone, null);
                    //file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.0.2. ".date("Y-m-d H:i:s")." rsUsers: " . print_r($rsUsers, 1) . PHP_EOL, FILE_APPEND);

                    // Пользователь не найден, регистрируем нового
                    if (empty($rsUsers)) {
                        // Пользователь не найден, регистрируем нового
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "3.0.0. ".date("Y-m-d H:i:s")." Регистрируем нового пользователя " . PHP_EOL , FILE_APPEND);

                        $user = new CUser;
                        $arFields = array(
                            "NAME" => '',
                            "LOGIN" => \firstbit\SmsHelpers::checkCodePhone($phone).$phone,
                            "EMAIL" => $phone.'@no-email.ru',
                            "PHONE_NUMBER" => $data['number_login'], // Номер телефона
                            "LID" => "s1",
                            "ACTIVE" => "Y",
                            "PASSWORD" => '!pass@word!'.date('YmdHi'),
                            "CONFIRM_PASSWORD" => '!pass@word!'.date('YmdHi'),
                            "GROUP_ID" => array(2, 3, 4)
                        );
                        $newUserID = $user->Add($arFields);

                        if ($newUserID) {
                            $result['res'] = 'NEW';
                            $result['link'] = OK_AUTH_LINK;
                            $USER->Authorize($newUserID);

                            // Новый код, обновляем у пользователя UF_SMS_CODE
                            $fields = [
                                "UF_SMS_CODE" => $data['code_auth'],
                                "UF_CODE_TIME" => date("d.m.Y H:i:s")
                            ];
                            $userUpdate = $USER->Update($USER->GetID(), $fields);
                            if ($userUpdate === false) {
                                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.1.0. ".date("Y-m-d H:i:s")." Новый user, Update UF_SMS_CODE: ERROR " . print_r($userUpdate, 1) . PHP_EOL, FILE_APPEND);
                            } else {
                                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.1.1. ".date("Y-m-d H:i:s")." Новый user, Update UF_SMS_CODE: OK | status" . print_r($userUpdate, 1)  . PHP_EOL, FILE_APPEND);
                            }

                            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "3.0.1. ".date("Y-m-d H:i:s")." Зарегистрирован новый пользователь, ID: " . print_r($newUserID, 1) . PHP_EOL , FILE_APPEND);
                        } else {
                            $result['res'] = 'NOREG';
                            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "3.0.2. ".date("Y-m-d H:i:s")." Ошибка регистрации: " . print_r($newUserID, 1) . PHP_EOL , FILE_APPEND);
                        }
                    } else if ($rsUsers['UF_SMS_CODE'] == $data['code_auth']) {
                        $auth = $USER->Authorize($rsUsers['ID']);
                        if ($auth) {
                            $result['res'] = 'AUTH';
                            $result['link'] = OK_AUTH_LINK;

                            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.0.3. ".date("Y-m-d H:i:s")." Authorize: OK, status: " . print_r($auth, 1) . PHP_EOL, FILE_APPEND);
                        } else {
                            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "2.0.4. ".date("Y-m-d H:i:s")." Authorize: ERROR, status: " . print_r($auth, 1) . PHP_EOL, FILE_APPEND);
                        }
                    } else {
                        $result['res'] = 'NO';
                    }
                }

            }
        }
        else{
            $result['res'] = 'no-code';
        }
        echo json_encode($result);
        break;
}

/**
 * Получение свойств пользователя по какому-то либо свойству.
 * @param $id
 * @param $phone
 * @param $code
 * @return array
 * @throws Exception
 */
function getUser($id = null, $phone = null, $code = null) {
    $arUser = null;

    if ($id OR $code) {
        if ($id)
            $filter = ['=ID' => $id];
        else
            $filter = ['=UF_SMS_CODE' => $code] ;

        $arUser = \Bitrix\Main\UserTable::getList([
            'filter' => $filter,
            'limit' => 1,
            'select' => ["ID", "LOGIN", "UF_SMS_CODE", "UF_CODE_TIME"],
        ])->fetch();

        $arPhoneNum = UserPhoneAuthTable::getList([
            'filter' => ['=USER_ID' => $arUser['ID']],
            'select' => ['PHONE_NUMBER'],
        ])->fetch();
    } else if($phone) {
        $arPhoneNum = \Bitrix\Main\UserPhoneAuthTable::getList([
            'filter' => ['=PHONE_NUMBER' => \firstbit\SmsHelpers::checkCodePhone($phone).$phone],
            'select' => ['USER_ID', 'PHONE_NUMBER'],
        ])->fetch();

        $arUser = \Bitrix\Main\UserTable::getList([
            'filter' => ['=ID' => $arPhoneNum['USER_ID']],
            'limit' => 1,
            'select' => ["ID", "LOGIN", "UF_SMS_CODE", "UF_CODE_TIME"],
        ])->fetch();
    }
    if ($arPhoneNum['PHONE_NUMBER'])
        $arUser['PHONE_NUMBER'] = $arPhoneNum['PHONE_NUMBER'];

    if ($arUser['UF_CODE_TIME']) {
        $dateCurrent = date('Y-m-d H:i:s');
        $dateAuthCode = $arUser['UF_CODE_TIME']->format('Y-m-d H:i:s');
        //$dateDiff = \firstbit\SmsHelpers::dateDiff($dateCurrent, $dateAuthCode);
        //$arUser['END_TIME_LIMMIT'] = $dateDiff['minutes'] > SMS_CODE_LIFETIME;
    }

    return $arUser;
}

function sendSms($phone, $mess) {
    $smsru = new SMSRU(SMSRU_API_KEY);
    $dataStd = new stdClass();
    $dataStd->to = (string) $phone;
    $dataStd->text = $mess;
    $sms = $smsru->send_one($dataStd);

    if ($sms->status == "OK") {
        $res['res'] = "OK";
        $res['mess'] = "Выслан код СМС";
        // test
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "3.0.0. ".date("Y-m-d H:i:s")." action: get_code | Отправка смс: " . $mess . " на телефон: " . $phone . PHP_EOL, FILE_APPEND);
    } else {
        $res['res'] = "NO";
        $res['mess'] = "СМС НЕ отправлен! Все плохо...";
        $res['status_code'] = $sms->status_code;
        $res['status_text'] = $sms->status_text;
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "3.0.1. ".date("Y-m-d H:i:s")." action: get_code | Ошибка отправки смс: " . print_r([$sms->status_code, $sms->status_text], 1) . PHP_EOL, FILE_APPEND);
    }
    return $res;
}

