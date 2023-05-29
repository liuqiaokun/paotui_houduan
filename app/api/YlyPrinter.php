<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class YlyPrinter extends Common
{
	public function index()
	{
		$oid = 72;
		$order = \think\facade\Db::name("dmh_school_order")->find($oid);
		$school = \think\facade\Db::name("school")->find($order["s_id"]);
		$config = new \app\api\yly\Config\YlyConfig("1035891399", "e98746daffe5ffed3f680f109537b94f");
		$client = new \app\api\yly\Oauth\YlyOauthClient($config);
		try {
			$token = \think\facade\Cache::get("yly_access_token");
			if ($token) {
				$token = json_decode($token);
			}
			if (!$token) {
				$token = $client->getToken();
				\think\facade\Cache::set("yly_access_token", json_encode($token), 864000);
				$token = \think\facade\Cache::get("yly_access_token");
			}
		} catch (Exception $e) {
			throw new \think\Exception("获取或更新access_token的次数,已超过最大限制! ");
			echo $e->getMessage() . "\n";
			print_r(json_decode($e->getMessage(), true));
			return null;
		}
		$access_token = $token->access_token;
		$print = new \app\api\yly\Api\PrintService($access_token, $config);
		$express_info = json_decode($order["express_info"], true);
		foreach ($express_info as $k => $v) {
			if ($v["code"]) {
				$content = "<FS><center>" . $school["s_name"] . "代取</center></FS>";
				$content .= "<FS>" . $order["sh_name"] . "-" . $order["sh_phone"] . "</FS> \r";
				$content .= $v["code"] . $v["specs"] . "\n";
				$content .= "收货地址：" . $order["sh_addres"] . "  费用：￥" . $v["price"] . "\n";
				$content .= "备注：" . $order["remarks"];
				try {
					print_r($print->index("4004579828", $content, time()));
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}
		}
	}
}