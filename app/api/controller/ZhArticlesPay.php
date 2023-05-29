<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhArticlesPay extends Common
{
	public function topPay($a_id, $wxapp_id, $s_id, $price, $day, $type, $uid)
	{
		$school = \think\facade\Db::name("school")->find($s_id);
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$insert["a_id"] = $a_id;
		$insert["ordersn"] = date("YmdHis") . rand(1000, 9999);
		$insert["wxapp_id"] = $wxapp_id;
		$insert["s_id"] = $s_id;
		$insert["uid"] = $uid;
		$insert["price"] = $price;
		$insert["type"] = $type;
		$insert["day"] = $day;
		$insert["pay_type"] = $setting["pay_type"];
		$insert["rate"] = $type == 1 ? $price : $price * $school["reward_rate"] / 100;
		$o_id = \think\facade\Db::name("article_pay")->insertGetId($insert);
		$order = \think\facade\Db::name("article_pay")->find($o_id);
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		try {
			$notify_url = "http://" . $_SERVER["HTTP_HOST"] . "/api/ZhArticlesPay/payResult/wxapp_id/" . $wxapp_id;
			$js_pay = \app\api\service\PaymentService::instance($wxapp_id)->create($user["openid"], $order["ordersn"], $order["price"], "文章", $setting["pay_type"], ["notify_url" => $notify_url]);
			return ["order" => $order, "paydata" => $js_pay];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	public function payResult()
	{
		$wxapp_id = input("wxapp_id");
		$pay_type = input("pay_type");
		if ($pay_type == 1) {
			$postStr = file_get_contents("php://input");
			$getData = json_decode($postStr, true);
			if ($getData["tradeStatus"] = "SUCCESS") {
				$this->handle($getData["outTradeNo"], $getData["payAmt"]);
			}
		} else {
			$app = \app\api\service\PaymentService::instance($wxapp_id)::getPayApp();
			$response = $app->handlePaidNotify(function ($message, $fail) {
				$order = \think\facade\Db::name("article_pay")->where(["ordersn" => $message["out_trade_no"]])->find();
				if (!$order || $order->status == 2) {
					return true;
				}
				if ($message["return_code"] === "SUCCESS" && $message["result_code"] === "SUCCESS") {
					$this->handle($message["out_trade_no"], $message["total_fee"]);
					return true;
				}
			});
		}
	}
	public function handle($ordersn, $fee)
	{
		$order = \think\facade\Db::name("article_pay")->where(["ordersn" => $ordersn])->find();
		if ($order["status"] == 0 && intval(bcmul($order["price"], 100)) == $fee) {
			$data = ["status" => 1, "pay_time" => date("Y-m-d H:i:s")];
			if ($order["type"] == 1) {
				$article = \think\facade\Db::name("zh_articles")->where("article_id", $order["a_id"])->find();
				if ($article["status"] == 1) {
					Distribution::index(5, $order["id"]);
				}
				\think\facade\Db::name("zh_articles")->where("article_id", $order["a_id"])->update(["deadtime" => strtotime("+" . $order["day"] . "day"), "is_expired" => 1, "paytime" => date("Y-m-d H:i:s")]);
				\think\facade\Db::name("article_pay")->where(["ordersn" => $order])->update($data);
			} else {
				Distribution::index(4, $order["id"]);
				$pay_user = \think\facade\Db::name("wechat_user")->find($order["uid"]);
				$article = \think\facade\Db::name("zh_articles")->find($order["a_id"]);
				$add_money = $order["price"] - $order["rate"];
				$insert = ["wxapp_id" => $order["wxapp_id"], "uid" => $article["u_id"], "type" => 1, "price" => $add_money, "operate" => 0, "remark" => "获得用户" . $pay_user["nickname"] . "打赏"];
				\think\facade\Db::name("user_account_log")->insert($insert);
				\think\facade\Db::name("wechat_user")->where("u_id", $article["u_id"])->inc("balance", $add_money)->update();
				\think\facade\Db::name("article_pay")->where(["ordersn" => $order])->update($data);
			}
		}
	}
	public function payResult_bak()
	{
		$wxapp_id = input("wxapp_id");
		trace("进入回调" . $wxapp_id, "notify_" . date("Y_m_d"));
		$app = \app\api\service\PaymentService::instance($wxapp_id)::getPayApp();
		$response = $app->handlePaidNotify(function ($message, $fail) {
			trace($message, "notify_" . date("Y_m_d"));
			$order = \think\facade\Db::name("article_pay")->where(["ordersn" => $message["out_trade_no"]])->find();
			if (!$order || $order->status == 2) {
				return true;
			}
			if ($message["return_code"] === "SUCCESS") {
				if ($message["result_code"] === "SUCCESS") {
					$data = ["status" => 1, "pay_time" => date("Y-m-d H:i:s")];
					if ($order["type"] == 1) {
						$article = \think\facade\Db::name("zh_articles")->where("article_id", $order["a_id"])->find();
						if ($article["status"] == 1) {
							Distribution::index(5, $order["id"]);
						}
						\think\facade\Db::name("zh_articles")->where("article_id", $order["a_id"])->update(["deadtime" => strtotime("+" . $order["day"] . "day"), "is_expired" => 1, "paytime" => date("Y-m-d H:i:s")]);
						\think\facade\Db::name("article_pay")->where(["ordersn" => $message["out_trade_no"]])->update($data);
					} else {
						Distribution::index(4, $order["id"]);
						$pay_user = \think\facade\Db::name("wechat_user")->find($order["uid"]);
						$article = \think\facade\Db::name("zh_articles")->find($order["a_id"]);
						$add_money = $order["price"] - $order["rate"];
						$insert = ["wxapp_id" => $order["wxapp_id"], "uid" => $article["u_id"], "type" => 1, "price" => $add_money, "operate" => 0, "remark" => "获得用户" . $pay_user["nickname"] . "打赏"];
						\think\facade\Db::name("user_account_log")->insert($insert);
						\think\facade\Db::name("wechat_user")->where("u_id", $article["u_id"])->inc("balance", $add_money)->update();
						\think\facade\Db::name("article_pay")->where(["ordersn" => $message["out_trade_no"]])->update($data);
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