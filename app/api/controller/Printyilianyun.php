<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

require_once "../vendor/yilianyun/Lib/Autoloader.php";
class Printyilianyun extends Common
{
	public function callPrint($id, $store_id)
	{
		$business = \think\facade\Db::name("zh_business")->where("business_id", $store_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->where("id", $id)->find();
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $order["wxapp_id"])->find();
		$config = new \App\Config\YlyConfig($business["clientid"], $business["clientsecret"]);
		$client = new \App\Oauth\YlyOauthClient($config);
		$token = \think\facade\Db::name("yilianyun_token")->where("clientid", $business["clientid"])->order("id desc")->find();
		$time = strtotime(date("Y-m-h", time()));
		if ($time == $token["addtime"]) {
			$token = json_decode($token["token"], true);
		} else {
			$token = $client->getToken();
			$datas = ["token" => json_encode($token), "addtime" => $time, "clientid" => $business["clientid"]];
			\think\facade\Db::name("yilianyun_token")->insert($datas);
			$token = json_decode(json_encode($token), true);
		}
		$printer = new \App\Api\PrinterService($token["access_token"], $config);
		$data = $printer->addPrinter($business["machine_code"], $business["msign"], "机器昵称也可不填", "gprs卡号没有可不填");
		$print = new \App\Api\PrintService($token["access_token"], $config);
		$content = self::getText($order, $setting);
		$data = $print->index($business["machine_code"], $content, $order["ordersn"]);
	}
	public function callPrints($id, $store_id)
	{
		$business = \think\facade\Db::name("zh_business")->where("business_id", $store_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->where("id", $id)->find();
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $order["wxapp_id"])->find();
		$config = new \App\Config\YlyConfig($business["clientid"], $business["clientsecret"]);
		$client = new \App\Oauth\YlyOauthClient($config);
		$token = \think\facade\Db::name("yilianyun_token")->where("clientid", $business["clientid"])->order("id desc")->find();
		$time = strtotime(date("Y-m-h", time()));
		if ($time == $token["addtime"]) {
			$token = $token["token"];
		} else {
			$token = $client->getToken();
			$datas = ["token" => json_encode($token), "addtime" => $time, "clientid" => $business["clientid"]];
			\think\facade\Db::name("yilianyun_token")->insert($datas);
			$token = json_decode(json_encode($token), true);
		}
		$printer = new \App\Api\PrinterService($token["access_token"], $config);
		$data = $printer->addPrinter($business["machine_code"], $business["msign"], "机器昵称也可不填", "gprs卡号没有可不填");
		$print = new \App\Api\PrintService($token["access_token"], $config);
		$content = self::cancel($order, $setting);
		$data = $print->index($business["machine_code"], $content, time());
	}
	public function printerLog($content)
	{
		$data["content"] = $content;
		$data["create_time"] = date("Y-m-d H:i:s");
		\think\facade\Db::name("print_log")->insertGetId($data);
	}
	public function getText($order, $setting)
	{
		$content = "<FS2><center>** " . $setting["app_name"] . " **</center></FS2>";
		$content .= "<FS2><center>** #" . $order["pick_number"] . " **</center></FS2>";
		$content .= str_repeat(".", 32);
		$content .= "<FS2><center>" . $order["business_name"] . "</center></FS2>\n";
		$content .= "下单时间:" . date("Y-m-d H:i", $order["createtime"]) . "\n\n";
		$content .= "订单编号:" . $order["ordersn"] . "\n\n";
		if ($order["is_self_lifting"] == 0) {
			if ($order["givetype"] == 1) {
				$content .= "送达时间:" . $order["ordertime"] . "\n\n";
			} else {
				$content .= "送达时间:" . date("Y-m-d H:i", $order["start_time"]) . "\n\n";
			}
		} else {
			$content .= "到店时间:" . $order["arrival_time"] . "\n\n";
		}
		$content .= "用户姓名:" . $order["sh_name"] . "\n\n";
		$content .= "用户手机号:" . $order["sh_phone"] . "\n\n";
		$content .= "收货地址:" . $order["sh_addres"] . "\n\n";
		if (!empty($order["remarks"])) {
			$content .= "备注：" . $order["remarks"] . "\n\n";
		}
		$content .= str_repeat("*", 14) . "商品" . str_repeat("*", 14);
		$content .= "<table>";
		$good_details = explode(";", $order["good_details"]);
		foreach ($good_details as $key => $v) {
			$content .= "<tr><td>" . $v . "</td></tr>";
		}
		$content .= "</table>";
		$content .= str_repeat(".", 32);
		$content .= str_repeat("*", 32);
		$content .= "订单总价:￥" . $order["food_money"] . " \n";
		$content .= "<FS2><center>**#" . $order["pick_number"] . " 完**</center></FS2>";
		return $content;
	}
	public function cancel($order, $setting)
	{
		$content = "<FS2><center>**" . $setting["app_name"] . "**</center></FS2>";
		$content .= "<FS2><center>** #" . $order["pick_number"] . " **</center></FS2>";
		$content .= str_repeat(".", 32);
		$content .= "<FS2><center>" . $order["business_name"] . "</center></FS2>\n";
		$content .= "下单时间:" . date("Y-m-d H:i", $order["createtime"]) . "\n\n";
		$content .= "订单编号:" . $order["ordersn"] . "\n\n";
		if ($order["is_self_lifting"] == 0) {
			if ($order["givetype"] == 1) {
				$content .= "送达时间:" . $order["ordertime"] . "\n\n";
			} else {
				$content .= "送达时间:" . date("Y-m-d H:i", $order["start_time"]) . "\n\n";
			}
		} else {
			$content .= "到店时间:" . $order["arrival_time"] . "\n\n";
		}
		$content .= "用户姓名:" . $order["sh_name"] . "\n\n";
		$content .= "用户手机号:" . $order["sh_phone"] . "\n\n";
		$content .= "收货地址:" . $order["sh_addres"] . "\n\n";
		if (!empty($order["remarks"])) {
			$content .= "备注：" . $order["remarks"] . "\n\n";
		}
		$content .= "客户申请取消订单\n\n";
		$content .= str_repeat("*", 14) . "商品" . str_repeat("*", 14);
		$content .= "<table>";
		$good_details = explode(";", $order["good_details"]);
		foreach ($good_details as $key => $v) {
			$content .= "<tr><td>" . $v . "</td></tr>";
		}
		$content .= "</table>";
		$content .= str_repeat(".", 32);
		$content .= str_repeat("*", 32);
		$content .= "订单总价:￥" . $order["food_money"] . " \n";
		$content .= "<FS2><center>**#" . $order["pick_number"] . " 完**</center></FS2>";
		return $content;
	}
}