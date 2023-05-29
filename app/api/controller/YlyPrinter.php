<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class YlyPrinter extends Common
{
	public function index($order)
	{
		$school = \think\facade\Db::name("school")->find($order["s_id"]);
		$express = \think\facade\Db::name("dmh_express")->find($order["express_id"]);
		file_put_contents("打印问题.txt", " 订单号 " . $order["id"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		if ($express["client_id"]) {
			$config = new \app\api\yly\Config\YlyConfig($express["client_id"], $express["client_secret"]);
			$client = new \app\api\yly\Oauth\YlyOauthClient($config);
			try {
				$token = \think\facade\Cache::get($express["client_id"] . "_access_token");
				if ($token) {
					$token = json_decode($token);
				}
				if (!$token) {
					$token = $client->getToken();
					\think\facade\Cache::set($express["client_id"] . "_access_token", json_encode($token));
				}
			} catch (\Exception $e) {
				file_put_contents("打印问题.txt", " 订单号 " . $order["id"] . "  错误 " . $e->getMessage() . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
			}
			$access_token = $token->access_token;
			$print = new \app\api\yly\Api\PrintService($access_token, $config);
			$express_info = json_decode($order["express_info"], true);
			foreach ($express_info as $k => $v) {
				$content = "**************************** \\r";
				$content .= "<FH2><FW2>" . $school["s_name"] . "代取</FH2></FW2> \r";
				$content .= "<FS2>" . $order["sh_name"] . "-" . $order["sh_phone"] . "</FS2> \r";
				$content .= "<FS2>" . $v["code"] . $v["specs"] . "</FS2> \n";
				$content .= "<FS2>快递点：" . $order["qu_addres"] . "</FS2> \n";
				$content .= "<FS2>收货地址：" . $order["sh_addres"] . "  费用：￥" . $v["price"] . "</FS2> \n";
				$content .= "备注：" . $order["remarks"];
				file_put_contents("打印问题.txt", " 订单号 " . $order["id"] . "  token  " . $access_token . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
				try {
					$print->index($express["machine_code"], $content, time());
				} catch (\Exception $e) {
					file_put_contents("打印问题.txt", " 订单号 " . $order["id"] . "  错误 " . $e->getMessage() . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
				}
			}
		}
	}
}