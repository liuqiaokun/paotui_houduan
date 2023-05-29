<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class PaymentService extends \xhadmin\CommonService
{
	protected static $config = [];
	protected static $site = [];
	protected static $PayApp;
	public function __construct()
	{
	}
	public static function instance($wxapp_id) : PaymentService
	{
		$site = \app\api\model\Setting::where(["wxapp_id" => $wxapp_id])->find();
		self::$config = ["app_id" => $site["appid"], "mch_id" => $site["mch_id"], "key" => $site["mch_key"], "cert_path" => $site["refund_cert"], "key_path" => $site["refund_key"]];
		self::$site = $site;
		self::$PayApp = \EasyWeChat\Factory::payment(self::$config);
		return new self();
	}
	public static function create($openid, $order_no, $total_fee, $title, $pay_type, $params)
	{
		$params["notify_url"] = $params["notify_url"] . "/pay_type/" . $pay_type;
		if ($pay_type == 1) {
			$url = "http://epay.fkynet.net/api/bestPay/bestPayOrder";
			$data = ["merchantNo" => self::$site["merchant_no"], "institutionCode" => self::$site["merchant_no"], "outTradeNo" => $order_no, "tradeAmt" => $total_fee * 100, "subject" => "用户下单", "tradeType" => "TENPAY", "subOpenId" => $openid, "requestDate" => date("Y-m-d H:i:s"), "timeOut" => 258200, "goodsInfo" => "购买", "notifyUrl" => $params["notify_url"], "operator" => "格创", "appId" => self::$site["appid"], "tradeScene" => "ONLINE", "payScene" => "APP"];
			$riskControlInfo = ["service_identify" => 10000001, "subject" => "商品消费", "product_type" => 2, "goods_count" => 1];
			$data["riskControlInfo"] = json_encode($riskControlInfo);
			$schemeParam = ["appid" => self::$site["appid"], "secret" => self::$site["appsecret"], "path" => "/pages/index/index"];
			$data["schemeParam"] = json_encode($schemeParam);
			$post["bestPayRequest"] = $data;
			$post["p12Password"] = self::$site["p12_password"];
			$post["p12Path"] = self::$site["private_key"];
			$post["cerPath"] = self::$site["public_key"];
			$result = \app\accounts\controller\HttpExtend::post($url, json_encode($post), ["headers" => ["Content-type:application/json"]]);
			$res = json_decode($result, true);
			if ($res["return_code"] == "SUCCESS") {
				$res["timestamp"] = $res["timeStamp"];
				return $res;
			} else {
				throw new \Exception($res["errorMsg"]);
			}
		} else {
			$result = self::$PayApp->order->unify(["body" => $title, "out_trade_no" => $order_no, "total_fee" => $total_fee * 100, "notify_url" => $params["notify_url"], "trade_type" => "JSAPI", "openid" => $openid]);
			if ($result["return_code"] == "SUCCESS") {
				if ($result["result_code"] == "SUCCESS") {
					$jssdk = self::$PayApp->jssdk;
					$pay_data = $jssdk->sdkConfig($result["prepay_id"]);
					return $pay_data;
				} else {
					throw new \Exception($result["return_msg"]);
				}
			} else {
				throw new \Exception($result["return_msg"]);
			}
		}
	}
	public static function refund($order_no, $refund_no, $totalFee, $refundFee, $pay_type)
	{
		if ($pay_type == 1) {
			$url = "http://epay.fkynet.net/api/bestPay/bestPayRefund";
			$data = ["merchantNo" => self::$site["merchant_no"], "institutionCode" => self::$site["merchant_no"], "OutTradeNo" => $order_no, "OutRequestNo" => time() . rand(10, 99), "refundAmt" => $totalFee * 100, "requestDate" => date("Y-m-d H:i:s"), "OriginalTradeDate" => date("Y-m-d") . " 00:00:00", "operator" => "格创", "notifyUrl" => "http://" . $_SERVER["HTTP_HOST"] . "/api/TestPay/refundnotify/wxapp_id/3", "appId" => self::$site["appid"], "tradeScene" => "ONLINE", "payScene" => "APP"];
			$post["bestPayRefundRequest"] = $data;
			$post["p12Password"] = self::$site["p12_password"];
			$post["p12Path"] = self::$site["private_key"];
			$post["cerPath"] = self::$site["public_key"];
			$result = \app\accounts\controller\HttpExtend::post($url, json_encode($post), ["headers" => ["Content-type:application/json"]]);
			$resultData = json_decode($result, true);
			if ($resultData["tradeStatus"] == "SUCCESS") {
				return $resultData;
			} else {
				throw new \Exception($resultData["errorMsg"]);
			}
		} else {
			$result = self::$PayApp->refund->byOutTradeNumber($order_no, $refund_no, strval($totalFee * 100), strval($refundFee * 100), []);
			\think\facade\Db::name("order_refund_log")->insert(["ordersn" => $order_no, "text" => json_encode($result), "err" => $result["err_code_des"]]);
			if ($result["return_code"] == "SUCCESS") {
				if ($result["result_code"] == "SUCCESS") {
					return $result;
				} else {
					throw new \Exception($result["err_code_des"]);
				}
			} else {
				throw new \Exception($result["return_msg"]);
			}
		}
	}
	public static function getConfig() : array
	{
		return self::$config;
	}
	public static function getPayApp()
	{
		return self::$PayApp;
	}
}