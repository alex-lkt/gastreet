<?
use Bitrix\Catalog\Product\Basket;
use Bitrix\Sale;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Internals\BasketTable;
use firstbit\Helpers;

define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=utf-8');
/** @var CUser $USER */
global $USER;
/** @var CMain $APPLICATION */
global $APPLICATION;

if (!$USER->IsAuthorized())
	Helpers::die_json_error("access denied");

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$type = htmlspecialcharsbx($request['type']) ?? false;
$result = ["success" => false, "data" => [], "errors" => []];

switch ($type) {
	case "get_count":
		$site_id = htmlspecialcharsbx($request['site_id']) ?? false;
        $result = Helpers::getBasketInfo($site_id);
        break;

	default:
		$result["msg"] = "unknown action";
		break;
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
