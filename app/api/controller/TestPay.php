<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class TestPay extends Common
{
	public function pays()
	{
		$openid = "ouMoB5OmtEswYP9YpPsVUoGqhasI";
		$config = \think\facade\Db::name("setting")->where("wxapp_id", 3)->find();
		try {
			$result = $this->pay($config, $openid);
			return $this->ajaxReturn($this->successCode, "操作成功", ["paydata" => $result]);
		} catch (\Exception $e) {
			return $this->ajaxReturn($this->errorCode, $e->getMessage());
		}
	}
	public function pay($config, $openid)
	{
		$url = "http://epay.fkynet.net/api/bestPay/bestPayOrder";
		$ordersn = time() . rand(10, 99);
		$data = ["merchantNo" => $config["merchant_no"], "institutionCode" => $config["merchant_no"], "outTradeNo" => $ordersn, "tradeAmt" => 1, "subject" => "用户下单", "tradeType" => "TENPAY", "subOpenId" => $openid, "requestDate" => date("Y-m-d H:i:s"), "timeOut" => 258200, "goodsInfo" => "购买", "notifyUrl" => "http://test.fkynet.net/api/TestPay/notify/wxapp_id/3", "operator" => "格创", "appId" => $config["appid"], "tradeScene" => "ONLINE", "payScene" => "APP"];
		$riskControlInfo = ["service_identify" => 10000001, "subject" => "商品消费", "product_type" => 2, "goods_count" => 1];
		$data["riskControlInfo"] = json_encode($riskControlInfo);
		file_put_contents("testpay.txt", " 下单生成订单号 " . $ordersn . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		$schemeParam = ["appid" => $config["appid"], "secret" => $config["appsecret"], "path" => "/pages/index/index"];
		$data["schemeParam"] = json_encode($schemeParam);
		$post["bestPayRequest"] = $data;
		$post["p12Password"] = $config["p12_password"];
		$post["p12Path"] = $config["private_key"];
		$post["cerPath"] = $config["public_key"];
		$result = \app\accounts\controller\HttpExtend::post($url, json_encode($post), ["headers" => ["Content-type:application/json"]]);
		$res = json_decode($result, true);
		if ($res["return_code"] == "SUCCESS") {
			return $res;
		} else {
			throw new \Exception($res["errorMsg"]);
		}
		return $this->ajaxReturn($this->successCode, "返回成功", json_decode($result, true));
	}
	public function notify()
	{
		$wxapp_id = input("wxapp_id");
		$postStr = file_get_contents("php://input");
		$getData = json_decode($postStr, true);
		if ($getData["tradeStatus"] = "SUCCESS") {
		}
		file_put_contents("testpay.txt", " 支付回调平台id " . $wxapp_id . " 订单号参数 " . $getData["outTradeNo"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		return "success";
	}
	public function refunds()
	{
		$refundAmt = 1;
		$ordersn = "165647160030";
		$config = \think\facade\Db::name("setting")->where("wxapp_id", 3)->find();
		try {
			$result = $this->refund($refundAmt, $ordersn, $config);
			return $this->ajaxReturn($this->successCode, "操作成功", $result);
		} catch (\Exception $e) {
			return $this->ajaxReturn($this->errorCode, $e->getMessage());
		}
	}
	public function refund($refundAmt, $ordersn, $config)
	{
		$url = "http://epay.fkynet.net/api/bestPay/bestPayRefund";
		$data["merchantNo"] = $config["merchant_no"];
		$data["institutionCode"] = $config["merchant_no"];
		$data["OutTradeNo"] = $ordersn;
		$data["OutRequestNo"] = time() . rand(10, 99);
		$data["refundAmt"] = $refundAmt;
		$data["requestDate"] = date("Y-m-d H:i:s");
		$data["OriginalTradeDate"] = date("Y-m-d") . " 00:00:00";
		$data["operator"] = "格创";
		$data["notifyUrl"] = "http://test.fkynet.net/api/TestPay/refundnotify/wxapp_id/3";
		$data["appId"] = $config["appid"];
		$data["tradeScene"] = "ONLINE";
		$data["payScene"] = "APP";
		$post["bestPayRefundRequest"] = $data;
		$post["p12Password"] = $config["p12_password"];
		$post["p12Path"] = $config["private_key"];
		$post["cerPath"] = $config["public_key"];
		$result = \app\accounts\controller\HttpExtend::post($url, json_encode($post), ["headers" => ["Content-type:application/json"]]);
		$resultData = json_decode($result, true);
		if ($resultData["tradeStatus"] == "SUCCESS") {
			return $resultData;
		} else {
			throw new \Exception($resultData["errorMsg"]);
		}
	}
	public function refundnotify()
	{
		$wxapp_id = input("wxapp_id");
		$postStr = file_get_contents("php://input");
		$getData = json_decode($postStr, true);
		file_put_contents("refundtestpay.txt", " 平台id " . $wxapp_id . " 订单号参数 " . $getData["outTradeNo"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		return "success";
	}
}