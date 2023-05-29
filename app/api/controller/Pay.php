<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Pay extends Common
{
	public function pay()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$type = $this->request->post("type");
		$price = $this->request->post("price");
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$order_id = "VIP" . time() . rand(10, 99) . rand(10, 99);
		$set_price = $type == 1 ? $setting["user_month_fee"] : $setting["user_year_fee"];
		if ($set_price != $price) {
			return $this->ajaxReturn($this->errorCode, "金额有误", 0);
		}
		$insert["wxapp_id"] = $wxapp_id;
		$insert["u_id"] = $uid;
		$insert["order_id"] = $order_id;
		$insert["price"] = $price;
		$insert["type"] = $type;
		$insert["pay_type"] = $setting["pay_type"];
		$res = \think\facade\Db::name("user_vip_record")->insert($insert);
		$notify_url = "https://" . $_SERVER["HTTP_HOST"] . "/api/Pay/notify";
		$js_pay = \app\api\service\PaymentService::instance($wxapp_id)->create($user["openid"], $order_id, $price, "会员", $setting["pay_type"], ["notify_url" => $notify_url]);
		return $this->ajaxReturn($this->successCode, "下单成功", $js_pay);
	}
	public function notify()
	{
		$pay_type = input("pay_type");
		$postStr = file_get_contents("php://input");
		if ($pay_type == 1) {
			$getData = json_decode($postStr, true);
			if ($getData["tradeStatus"] = "SUCCESS") {
				$ordersn = $getData["outTradeNo"];
				$price = $getData["payAmt"];
			} else {
				return false;
			}
		} else {
			$getData = $this->xmlstr_to_array($postStr);
			if ($getData["return_code"] === "SUCCESS" && $getData["result_code"] === "SUCCESS") {
				$ordersn = $getData["out_trade_no"];
				$price = $getData["total_fee"];
			} else {
				return false;
			}
		}
		file_put_contents("huidiao.txt", " 订单号参数 " . json_encode($getData) . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		$order = \think\facade\Db::name("user_vip_record")->where("order_id", $ordersn)->find();
		if ($price == intval(bcmul($order["price"], 100)) && $order["status"] == 0) {
			\think\facade\Db::name("user_vip_record")->where("order_id", $ordersn)->update(["status" => 1, "pay_time" => date("Y-m-d H:i:s")]);
			$user = \think\facade\Db::name("wechat_user")->find($order["u_id"]);
			if (!$user["deadtime"]) {
				$dead_time = $order["type"] == 1 ? strtotime("1month") : strtotime("1year");
			} else {
				$dead_time = $order["type"] == 1 ? strtotime("1month", $user["deadtime"]) : strtotime("1year", $user["deadtime"]);
			}
			\think\facade\Db::name("wechat_user")->where("u_id", $user["u_id"])->update(["deadtime" => $dead_time]);
		}
		$str = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
		echo $str;
	}
	public function pay_bak()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$type = $this->request->post("type");
		$price = $this->request->post("price");
		$peiz = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$config = ["app_id" => $peiz["appid"], "mch_id" => $peiz["mch_id"], "key" => $peiz["mch_key"], "notify_url" => "https://" . $_SERVER["HTTP_HOST"] . "/api/Pay/notify"];
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$app = \EasyWeChat\Factory::payment($config);
		$openid = $user["openid"];
		$order_id = "VIP" . time() . rand(10, 99) . rand(10, 99);
		$pay_info = ["trade_type" => "JSAPI", "body" => "测试订单", "out_trade_no" => $order_id, "total_fee" => $price * 100, "openid" => $openid];
		$insert["wxapp_id"] = $wxapp_id;
		$insert["u_id"] = $uid;
		$insert["order_id"] = $order_id;
		$insert["price"] = $price;
		$insert["type"] = $type;
		$res = \think\facade\Db::name("user_vip_record")->insert($insert);
		$result = $app->order->unify($pay_info);
		if ($res && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
			$appId = $result["appid"];
			$nonceStr = $result["nonce_str"];
			$prepay_id = $result["prepay_id"];
			$timeStamp = time();
			$key = $peiz["mch_key"];
			$paySign = md5("appId={$appId}&nonceStr={$nonceStr}&package=prepay_id={$prepay_id}&signType=MD5&timeStamp={$timeStamp}&key={$key}");
			$config = [];
			$config["nonceStr"] = $nonceStr;
			$config["timeStamp"] = strval($timeStamp);
			$config["package"] = "prepay_id=" . $prepay_id;
			$config["paySign"] = $paySign;
			$config["signType"] = "MD5";
			return $this->ajaxReturn($this->successCode, "下单成功", $config);
		}
		if ($result["return_code"] == "FAIL" && array_key_exists("return_msg", $result)) {
			return $this->ajaxReturn($this->errorCode, "错误", $result["return_msg"]);
		}
		return $this->ajaxReturn($this->errorCode, "错误", $result["err_code_des"]);
	}
	public function notify_bak()
	{
		$postStr = file_get_contents("php://input");
		$getData = $this->xmlstr_to_array($postStr);
		file_put_contents("huidiao.txt", " 订单号参数 " . $getData["out_trade_no"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		$order = \think\facade\Db::name("user_vip_record")->where("order_id", $getData["out_trade_no"])->find();
		if ($order && $order["status"] == 0 && $getData["return_code"] === "SUCCESS") {
			if ($getData["result_code"] === "SUCCESS" && $getData["total_fee"] == intval(bcmul($order["price"], 100))) {
				\think\facade\Db::name("user_vip_record")->where("order_id", $getData["out_trade_no"])->update(["status" => 1, "pay_time" => date("Y-m-d H:i:s")]);
				$user = \think\facade\Db::name("wechat_user")->where("wxapp_id", $order["wxapp_id"])->where("openid", $getData["openid"])->find();
				if (!$user["deadtime"]) {
					$dead_time = $order["type"] == 1 ? strtotime("1month") : strtotime("1year");
				} else {
					$dead_time = $order["type"] == 1 ? strtotime("1month", $user["deadtime"]) : strtotime("1year", $user["deadtime"]);
				}
				\think\facade\Db::name("wechat_user")->where("u_id", $user["u_id"])->update(["deadtime" => $dead_time]);
			}
		}
		$str = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
		echo $str;
	}
	public function xmlstr_to_array($xmlstr)
	{
		libxml_disable_entity_loader(true);
		$xmlstring = simplexml_load_string($xmlstr, "SimpleXMLElement", LIBXML_NOCDATA);
		$val = json_decode(json_encode($xmlstring), true);
		return $val;
	}
}