<?
namespace firstbit;

class Helpers{
	
	public static function die_json_error($msg = "", $data = [])
	{
		echo json_encode([
			"success" => false,
			"msg" => $msg,
			"data" => $data,
		], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		die();
	}
}