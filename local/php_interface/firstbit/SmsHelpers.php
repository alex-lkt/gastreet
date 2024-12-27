<?
namespace firstbit;

use Bitrix\Main\Type\DateTime;

class SmsHelpers{
	/**
	 * Форматирование телефонного номера
	 * по шаблону и маске для замены
	 *
	 * @param string $phone
	 * @param string|array $format
	 * @param string $mask
	 * @return bool|string
	 */
	public static function phone_format($phone, $mask = '#')
	{
	    $format = [
	        '7' => '#######',
	        '10' => '7##########',
	        '11' => '###########'
	    ];

	    $rusPrefix = ['7', '8'];

	    $phone = preg_replace('/[^0-9]/', '', $phone);

	    if (is_array($format)) {
	        if (array_key_exists(strlen($phone), $format)) {
	            $format = $format[strlen($phone)];
	        } else {
	            return false;
	        }
	    }

	    $pattern = '/' . str_repeat('([0-9])?', substr_count($format, $mask)) . '(.*)/';

	    $format = preg_replace_callback(
	        str_replace('#', $mask, '/([#])/'),
	        function () use (&$counter) {
	            return '${' . (++$counter) . '}';
	        },
	        $format
	    );

	    $res = trim(preg_replace($pattern, $format, $phone, 1));
	    //file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "6.0.0. ".date("Y-m-d H:i:s")." data: " . print_r($res, 1) . PHP_EOL, FILE_APPEND);

	    if (!$res)
	        return false;

	    $sOne = mb_substr($res, 0, 1);
	    //file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "6.0.1. ".date("Y-m-d H:i:s")." data: " . print_r([$sOne, $res], 1) . PHP_EOL, FILE_APPEND);

	    if (in_array($sOne, $rusPrefix) !== false) {
	        $res = mb_substr($res, 1);
	    }

	    //file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sms_test.txt", "6.0.2. ".date("Y-m-d H:i:s")." data: " . print_r($res, 1) . PHP_EOL, FILE_APPEND);
	    return $res;
	}

	/**
	 * Разница между датами. Даты - объекты \Bitrix\Main\Type\DateTime
	 * Возвращает массив типа [[days] => 0, [hours] => 1, [minutes] => 83]]
	 * @param $date1
	 * @param $date2
	 * @return array
	 * @throws Exception
	 */
	public static function dateDiff($date1, $date2)
	{
	    //$time = new DateTime($date1, "Y-m-d H:i:s");
	    $time = DateTime::createFromPhp(new \DateTime($date1));

		$since_time = $time->diff( new DateTime($date2) );

		$result['days'] = $since_time->days;
		$result['hours'] = $since_time->days * 24 + $since_time->h;
		$result['minutes'] = ($since_time->days * 24 * 60) + ($since_time->h * 60) + $since_time->i;

		return $result;
	}
}
