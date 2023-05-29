<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class OrderPlus extends Common
{
	public function addPlus()
	{
		$price = $this->request->post("price");
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		if (!preg_match("/^[0-9]+(\\.\\d{1,2})?\$/", $price)) {
			return $this->ajaxReturn($this->errorCode, "价格有误");
		}
		$uid = $this->request->uid;
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$orderInfo = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$ordersn = date("YmdHis") . rand(1000, 9999);
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$insert["ordersn"] = $ordersn;
		$insert["wxapp_id"] = $wxapp_id;
		$insert["s_id"] = $orderInfo["s_id"];
		$insert["o_id"] = $o_id;
		$insert["u_id"] = $uid;
		$insert["price"] = $price;
		$insert["pay_type"] = $config["pay_type"];
		$insert["order_status"] = $orderInfo["status"];
		$res = \think\facade\Db::name("dmh_school_order_plus_price")->insertGetId($insert);
		try {
			$notify_url = "http://" . $this->request->host() . "/api/OrderPlus/payResult/wxapp_id/" . $wxapp_id;
			$js_pay = \app\api\service\PaymentService::instance($wxapp_id)->create($user["openid"], $ordersn, $price, "下单", $config["pay_type"], ["notify_url" => $notify_url]);
			return $this->ajaxReturn($this->successCode, "操作成功", ["order" => $insert, "paydata" => $js_pay]);
		} catch (\Exception $e) {
			return $this->ajaxReturn($this->errorCode, $e->getMessage());
		}
	}
	public function handle($ordersn, $fee)
	{
		$order = \think\facade\Db::name("dmh_school_order_plus_price")->where("ordersn", $ordersn)->find();
		file_put_contents("huidiao.txt", " 111订单号参数 " . $ordersn . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		$data = ["pay_status" => 1, "pay_time" => date("Y-m-d H:i:s")];
		if ($order["pay_status"] == 0 && $fee == intval(bcmul($order["price"], 100))) {
			\think\facade\Db::name("dmh_school_order_plus_price")->where("ordersn", $ordersn)->update($data);
			\think\facade\Db::name("dmh_school_order")->where("id", $order["o_id"])->inc("total", $order["price"])->update();
			$orderInfo = \think\facade\Db::name("dmh_school_order")->find($order["o_id"]);
			if ($order["order_status"] == 3) {
				$school = \think\facade\Db::name("school")->find($order["s_id"]);
				$plat_rate = \floatval($orderInfo["total"]) * \floatval($school["plat_rate"]) / 100;
				$school_rate = \floatval($orderInfo["total"]) * \floatval($school["school_rate"]) / 100;
				\think\facade\Db::name("dmh_school_order")->where("id", $order["o_id"])->update(["s_money" => $plat_rate, "fxs_money" => $school_rate]);
			}
		}
	}
	public function payResult()
	{
		$wxapp_id = input("wxapp_id");
		$pay_type = input("pay_type");
		$postStr = file_get_contents("php://input");
		if ($pay_type == 1) {
			$getData = json_decode($postStr, true);
			if ($getData["tradeStatus"] = "SUCCESS") {
				$this->handle($getData["outTradeNo"], $getData["payAmt"]);
			} else {
				return false;
			}
		} else {
			$app = \app\api\service\PaymentService::instance($wxapp_id)::getPayApp();
			$response = $app->handlePaidNotify(function ($message, $fail) {
				if ($message["return_code"] === "SUCCESS" && $message["result_code"] === "SUCCESS") {
					$this->handle($message["out_trade_no"], $message["total_fee"]);
				}
			});
		}
		return true;
	}
	public function payResults()
	{
		$wxapp_id = input("wxapp_id");
		trace("进入回调" . $wxapp_id, "notify_" . date("Y_m_d"));
		$app = \app\api\service\PaymentService::instance($wxapp_id)::getPayApp();
		$response = $app->handlePaidNotify(function ($message, $fail) {
			file_put_contents("cshd.txt", "时间" . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
			trace($message, "notify_" . date("Y_m_d"));
			$order = \think\facade\Db::name("dmh_school_order_plus_price")->where("ordersn", $message["out_trade_no"])->find();
			if (!$order || $order->pay_status == 1) {
				return true;
			}
			if ($message["return_code"] === "SUCCESS") {
				if ($message["result_code"] === "SUCCESS") {
					$data = ["pay_status" => 1, "pay_time" => date("Y-m-d H:i:s")];
					\think\facade\Db::name("dmh_school_order_plus_price")->where("ordersn", $message["out_trade_no"])->update($data);
					\think\facade\Db::name("dmh_school_order")->where("id", $order["o_id"])->inc("total", $order["price"])->update();
					$orderInfo = \think\facade\Db::name("dmh_school_order")->find($order["o_id"]);
					if ($order["order_status"] == 3) {
						$school = \think\facade\Db::name("school")->find($order["s_id"]);
						$plat_rate = \floatval($orderInfo["total"]) * \floatval($school["plat_rate"]) / 100;
						$school_rate = \floatval($orderInfo["total"]) * \floatval($school["school_rate"]) / 100;
						\think\facade\Db::name("dmh_school_order")->where("id", $order["o_id"])->update(["s_money" => $plat_rate, "fxs_money" => $school_rate]);
					}
				}
			} else {
				return $fail("通信失败，请稍后再通知我");
			}
			return true;
		});
		return $response->send();
	}
}