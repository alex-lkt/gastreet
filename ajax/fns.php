<?php
define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=utf-8');
/** @var CUser $USER */
global $USER;
/** @var CMain $APPLICATION */
global $APPLICATION;

$request = json_decode(file_get_contents('php://input'), true);

$type = htmlspecialcharsbx($request['type']) ?? false;
$result = ["success" => false, "data" => [], "errors" => []];

switch ($type) {
    case "get_inn":
        $numInn = htmlspecialcharsbx($request['number_inn']) ?? false;

        $key='8f4d1351a0a278bfc98d57a7a56061ab33126bad'; // FNS_API_KEY
        $result = file_get_contents("https://api-fns.ru/api/egr?req=".$numInn."&key=".$key);
        break;

    default:
        $result["msg"] = "unknown action";
        break;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
