<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Index extends Common
{
	public function getAccessToken($config)
	{
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $config["appid"] . "&secret=" . $config["appsecret"];
		$res = json_decode(file_get_contents($url), true);
		$access_token = $res["access_token"];
		return $access_token;
	}
	public function daodain()
	{
		$data = ["status" => 1, "pay_time" => time()];
		$data["store_money"] = 3;
		$data["fx_store_money"] = 4;
		print_r($data);
		exit;
		$arrival = "17:55";
		$end = strtotime(date("Y-m-d " . $arrival));
		$now = strtotime("+30 minute");
		if (strtotime("+30 minute") > strtotime(date("Y-m-d " . $arrival))) {
			echo "不可取消";
		} else {
			echo "可以取消";
		}
		print_r($end);
	}
	public function changeStock()
	{
		\think\facade\Db::name("zh_goods")->where("stock", 0)->update(["stock" => 999]);
	}
	public function purchase()
	{
		$val = md5($_SERVER["HTTP_HOST"]);
		$root = base64_decode("c3RhdGljL2pzL3hoZWRpdG9yL3NlcnZlcnNjcmlwdC9waHAv");
		if (!file_get_contents($root . $val . ".txt")) {
			file_put_contents($root . $val . ".txt", md5("xagc" . $_SERVER["HTTP_HOST"]) . "--" . time() . PHP_EOL, FILE_APPEND);
		}
		print_r(file_get_contents($root . $val . ".txt"));
		exit;
		exit;
		if (!!\think\facade\Db::query("DESCRIBE gc_zh_business balance")) {
			\think\facade\Db::execute("ALTER TABLE `gc_zh_business` CHANGE `balance` `balance` INT NULL DEFAULT '1' COMMENT '金豆余额';");
		}
		exit;
		print_r(\think\facade\Cache::get("aabbcc"));
		exit;
		$order = \think\facade\Db::name("dmh_school_order")->find(632);
		YlyPrinter::index($order);
		exit;
		$url = "http://send.fkynet.net/api/Plugins/is_purchase";
		$res = file_get_contents($url . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=1");
		print_r(json_decode($res, true));
		exit;
	}
	public function rice()
	{
		$host = "test.fkynet.net";
		$data = \think\facade\Db::name("zh_business")->where("business_image", "like", "%" . $host . "%")->select();
		print_r(count($data));
		exit;
		$latitude = "34.22259";
		$longitude = "108.94878";
		$data = \think\facade\Db::query("SELECT business_id,business_name,latitude,longitude,ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN(( " . $latitude . " * PI() / 180 - latitude * PI() / 180) / 2), 2) + COS(" . $latitude . " * PI() / 180) * COS(latitude * PI() / 180) * POW(SIN((" . $longitude . " * PI() / 180 - longitude * PI() / 180) / 2 ),2 )))) AS juli FROM gc_zh_business where s_id=8 and where juli > 5 ORDER BY juli asc");
		print_r($data);
	}
	public function testupdate()
	{
//		$url1 = "http://send.fkynet.net/api/Version/check";
        $url1 = "";

        $res = file_get_contents($url1 . "?linkadr=test.fkynet.net&wxapp_id=1");
		$res = json_decode($res, true);
		if ($res["info"]["enddate"] < time() && $res["status"] == 666) {
			return json(["status" => "01", "data" => "", "msg" => "该域名服务费已到期，请联系管理员续费"]);
		}
		if ($res["status"] == 666) {
			return json(["status" => "00", "data" => $res["url"], "msg" => "验证成功，正在更新。。。"]);
		} else {
			return json(["status" => "01", "data" => "", "msg" => "该域名为盗版"]);
		}
	}
	public function city()
	{
		if (!!\think\facade\Db::query("DESCRIBE gc_zh_articles image")) {
			\think\facade\Db::execute("ALTER TABLE `gc_zh_articles` CHANGE `image` `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片'");
		}
		exit;
		$path = "gc_school/pages/home/index";
		$config = ["app_id" => "wxb40cc4d3fb44c393", "secret" => "915d3946e75da1800f16737b1ae67ad7", "response_type" => "array"];
		$app = \EasyWeChat\Factory::miniProgram($config);
		$response = $app->app_code->getUnlimit("spid=1", ["page" => $path, "width" => 50]);
		if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
			$qr_filename = "https://" . $_SERVER["HTTP_HOST"] . "/doc/" . $response->saveAs("doc/", "mp" . rand(1, 10) . ".png");
		}
		print_r($qr_filename);
		exit;
	}
	public function testsss()
	{
		MsgSubscribe::cancelOrderRider(1023, 3, "ouMoB5AKEHFnyXecKXC_cj-4AU-k");
		exit;
		print_r(chr(rand(65, 90)));
		print_r(chr(rand(97, 122)));
		exit;
		$order = \think\facade\Db::name("dmh_school_order")->where("arrival_time", "<", date("H:i", strtotime("-2 hour")))->field("id")->select()->toArray();
		$a1 = [1, 2];
		$a4 = array_merge($order, $a1);
		print_r($a4);
		exit;
		$order["food_money"] = 25.9;
		$order["store_money"] = 2.59;
		$order["fx_store_money"] = 2.07;
		$store_money = intval(\floatval($order["food_money"]) - \floatval($order["store_money"]) - \floatval($order["fx_store_money"]) * 100) / 100;
		print_r($store_money);
		exit;
		$res = \think\facade\Db::name("wechat_user")->where("u_id", 2)->inc("balance", 1)->update();
		print_r($res);
		exit;
	}
	public function addBalance()
	{
		\think\facade\Db::name("zh_business")->where("balance", 0)->update(["balance" => 20]);
	}
	public function changeUtf()
	{
		define("ROOT_PATH", str_replace("/public", "/", $_SERVER["DOCUMENT_ROOT"]));
		$result = file_get_contents(ROOT_PATH . ".env");
		if (!strpos($result, "utf8mb4")) {
			file_put_contents(ROOT_PATH . ".env", str_replace("utf8", "utf8mb4", $result));
		}
	}
	public function wotest()
	{
		$ordersn = $this->request->post("ordersn", "", "serach_in");
		file_put_contents("quxiao.txt", " 订单号 " . $ordersn . " 取消时间" . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
	}
	public function testren()
	{
		print_r("ALTER DATABASE `" . \think\facade\Env::get("database.database") . "`   DEFAULT CHARACTER SET  utf8mb4");
		exit;
		\think\facade\Db::execute("ALTER DATABASE `" . \think\facade\Env::get("database.database") . "` DEFAULT CHARACTER SET  utf8mb4 COLLATE utf8mb4_unicode_ci");
		\think\facade\Db::execute("ALTER TABLE `gc_zh_articles` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
		if (!!\think\facade\Db::query("DESCRIBE gc_zh_articles content")) {
			\think\facade\Db::execute("ALTER TABLE `gc_zh_articles` CHANGE `content` `content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '内容';");
		}
		exit;
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", 3)->find();
		$is_open_pay = !is_null($setting["is_open_pays"]) ? $setting["is_open_pays"] : 1;
		print_r($is_open_pay);
		exit;
		$a = $_GET["goods_det"];
		$json = json_decode($a, true);
		$total_prices = 0;
		foreach ($json as $k => $v) {
			$information = \think\facade\Db::name("zh_goods")->alias("a")->join("zh_goods_type b ", "b.goods_type_id= a.goods_type_id")->join("zh_business c", "c.business_id = a.business_id")->where(["a.wxapp_id" => 3, "a.s_id" => 8, "a.id" => $v["ids"]])->find();
			$specs = json_decode(html_entity_decode($information["specs"]), true);
			if ($v["specs"] && count($specs["list"]) > 0) {
				foreach ($specs["list"] as &$v1) {
					if ($v["specs"] == $v1["type"]) {
						$total_prices += $v["nums"] * $v1["price"];
						$json[$k]["price"] = $v1["price"];
						$json[$k]["sum_price"] = round($v["nums"] * $v1["price"], 2);
					}
				}
			} else {
				$total_prices += $v["nums"] * $information["price"];
				$json[$k]["price"] = $information["price"];
				$json[$k]["sum_price"] = round($v["nums"] * $information["price"], 2);
			}
			if ($v["specs"] && $v["attribute"]) {
				$json[$k]["goods_name"] = $information["goods_name"] . "[" . $v["specs"] . "," . $v["attribute"] . "]";
			} else {
				if ($v["specs"] && !$v["attribute"]) {
					$json[$k]["goods_name"] = $information["goods_name"] . "[" . $v["specs"] . "]";
				} else {
					if (!$v["specs"] && $v["attribute"]) {
						$json[$k]["goods_name"] = $information["goods_name"] . "[" . $v["attribute"] . "]";
					} else {
						$json[$k]["goods_name"] = $information["goods_name"];
					}
				}
			}
			$json[$k]["cate_name"] = $information["goods_type_name"];
			$json[$k]["store_name"] = $information["business_name"];
		}
		$data = ["data" => $json, "total_prices" => round($total_prices, 2)];
		return $this->ajaxReturn($this->successCode, "操作成功", $data);
	}
	public function good()
	{
		$data = \think\facade\Db::name("zh_goods")->find(41);
		$specs = json_decode(html_entity_decode($data["specs"]), true);
		print_r($specs);
	}
	public function paytouser($openid = "oHZ-b5ZHVvLs66FF3qCFxkwYJDhI", $price = "0.3", $wxapp_id = 3)
	{
		$url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$urls = "http://" . $_SERVER["HTTP_HOST"];
		$path_cert = $_SERVER["DOCUMENT_ROOT"] . str_replace($urls, "", $config["refund_cert"]);
		$path_key = $_SERVER["DOCUMENT_ROOT"] . str_replace($urls, "", $config["refund_key"]);
		$onoce_str = $this->getRandChar(32);
		$api_key = $config["mch_key"];
		$data = ["mch_appid" => $config["appid"], "mchid" => $config["mch_id"], "nonce_str" => $onoce_str, "partner_trade_no" => date("YmdHis") . rand(1000, 9999), "openid" => $openid, "check_name" => "NO_CHECK", "amount" => $price * 100, "desc" => "用户提现", "spbill_create_ip" => $_SERVER["SERVER_ADDR"]];
		$str = "";
		foreach ($data as $k => $v) {
			$str .= $k . "=" . $v . "&";
		}
		$data["sign"] = $this->getSign($data, $api_key);
		$xml = $this->arrayToXml($data);
		$response_xml = $this->curl($xml, $url, $path_cert, $path_key);
		$response = $this->xmlstr_to_array($response_xml);
		print_r($response);
		exit;
	}
	public function msgtest()
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", 3)->find();
		$user = \think\facade\Db::name("wechat_user")->find(2);
		$data = [];
		$data["touser"] = $user["openid"];
		$data["template_id"] = $config["template_new"];
		$data["page"] = "gc_school/pages/article/detail";
		$data["data"] = ["character_string4" => ["value" => "232132"], "amount3" => ["value" => 0.01], "thing10" => ["value" => "看详情"], "date6" => ["value" => date("Y-m-d H:i:s", time())]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public function sendSubscribeMessage($uid = 2, $wxapp_id = 3)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$access_token = $this->getAccessToken($config);
		$url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=" . $access_token;
		$data = [];
		$data["touser"] = $user["openid"];
		$data["template_id"] = $config["template_new"];
		$data["page"] = "gc_school/pages/article/detail";
		$data["data"] = ["character_string4" => ["value" => "232132"], "amount3" => ["value" => 0.01], "thing10" => ["value" => "看详情"], "date6" => ["value" => date("Y-m-d H:i:s", time())]];
		$data["miniprogram_state"] = "formal";
		print_r(self::curlPost($url, json_encode($data)));
	}
	public function modify()
	{
		$origin_str = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/test.txt");
		$l1 = "siteroot: 'https://gcwe7.link1024.com/app/index.php'";
		$l2 = "siteroot: 'https://" . $_SERVER["HTTP_HOST"] . "/app/index.php'";
		$update_str1 = str_replace("uniacid: '2'", "uniacid: '3'", $origin_str);
		$update_str = str_replace($l1, $l2, $update_str1);
		file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/test.txt", $update_str);
	}
	public function canshu()
	{
		$postField = "a,b,c,d,e";
		$data = $this->request->get(explode(",", $postField));
		print_r($data);
		exit;
	}
}