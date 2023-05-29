<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class AutoUpdate extends Common
{
	public function AutoCancel()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if ($wxapp_id) {
			$order = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("out_time", "<", time())->where("status", 2)->select();
			$update["status"] = 8;
			foreach ($order as &$v) {
				if ($v["type"] == 3) {
					$goods = json_decode(html_entity_decode($v["goods_list"]), true);
					foreach ($goods as $k1 => $v1) {
						\think\facade\Db::name("zh_goods")->where("id", $v1["ids"])->inc("stock", $v1["nums"])->update();
					}
				}
				$plus = \think\facade\Db::name("dmh_school_order_plus_price")->where("o_id", $v["id"])->where("pay_status", 1)->where("is_refund", 0)->select()->toArray();
				if (count($plus) > 0) {
					try {
						$this->plusRefund($plus, $wxapp_id);
					} catch (\Exception $e) {
					}
				}
				file_put_contents("自动取消.txt", " 订单号 " . $v["id"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
				if ($v["t_money"] > 0) {
					try {
						\app\api\service\PaymentService::instance($wxapp_id)->refund($v["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $v["t_money"], $v["t_money"], $v["pay_type"]);
						$res = \think\facade\Db::name("dmh_school_order")->where("id", $v["id"])->update($update);
						file_put_contents("自动退款记录.txt", " 订单号 " . $v["ordersn"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
					} catch (\Exception $e) {
						file_put_contents("自动退款记录.txt", " 订单号 " . $v["ordersn"] . " 错误原因 " . $e->getMessage() . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
					}
				} else {
					$res = \think\facade\Db::name("dmh_school_order")->where("id", $v["id"])->update($update);
				}
				if ($res) {
				}
			}
		}
	}
	public function plusRefund($data, $wxapp_id)
	{
		foreach ($data as &$v) {
			try {
				\app\api\service\PaymentService::instance($wxapp_id)->refund($v["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $v["price"], $v["price"], $v["pay_type"]);
				\think\facade\Db::name("dmh_school_order")->where("id", $v["o_id"])->dec("total", $v["price"])->update();
				\think\facade\Db::name("dmh_school_order_plus_price")->where(["id" => $v["id"]])->update(["is_refund" => 1, "refund_time" => date("Y-m-d H:i:s")]);
			} catch (\Exception $e) {
				throw new \Exception("退款失败");
			}
		}
	}
	public function AutoFinish()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if ($wxapp_id) {
			$setting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
			$hour = $setting["order_finish_time"] ? $setting["order_finish_time"] : 24;
			$order = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("createtime", "<", strtotime("-" . $hour . " hour"))->where("status", 7)->select()->toArray();
			$orders = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("createtime", "<", strtotime("-" . $hour . " hour"))->where("status", 11)->select()->toArray();
			$order = array_merge($order, $orders);
			$update["status"] = 4;
			foreach ($order as &$v) {
				$school = \think\facade\Db::name("school")->find($v["s_id"]);
				Distribution::index(2, $v["id"]);
				\think\facade\Db::startTrans();
				try {
					file_put_contents("自动完成.txt", " 订单号 " . $v["id"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
					if ($v["is_open_pay"] == 1 && $v["type"] == 3) {
						$add_money = \floatval($v["total"]) - \floatval($v["s_money"]) - \floatval($v["fxs_money"]) + \floatval($v["food_money"]);
					} else {
						$add_money = \floatval($v["total"]) - \floatval($v["s_money"]) - \floatval($v["fxs_money"]);
					}
					$store_res1 = true;
					$store_res2 = true;
					$store_res3 = true;
					$store_res4 = true;
					if ($v["is_open_pay"] == 0 && $v["type"] == 3) {
						$money = bcsub($v["food_money"], $v["store_money"], 2);
						$store_money = bcsub($money, $v["fx_store_money"], 2);
						$store = \think\facade\Db::name("zh_business")->find($v["store_id"]);
						$store_money = bcsub($store_money, $store["deliveryfee"], 2);
						file_put_contents("finish.txt", "订单号" . $v["id"] . "  商家佣金  " . $store_money . "  商家id  " . $v["store_id"] . "  商家管理员  " . $store["wxadmin_name"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
						if ($store_money > 0 && $store["wxadmin_name"] > 0) {
							$store_res1 = \think\facade\Db::name("wechat_user")->where("u_id", $store["wxadmin_name"])->inc("store_balance", $store_money)->update();
							$store_res2 = \think\facade\Db::name("user_account_log")->insert(["wxapp_id" => $wxapp_id, "uid" => $store["wxadmin_name"], "price" => $store_money, "type" => 3, "operate" => 0, "remark" => "商家订单收入，订单号" . $v["id"]]);
						}
					}
					if ($school["store_bean_switch"] == 1 && $v["type"] == 3) {
						$school["deduction_num"] = $school["deduction_num"] ? $school["deduction_num"] : 0;
						if ($school["deduction_num"] > 0) {
							$store_res3 = \think\facade\Db::name("zh_business")->where("business_id", $v["store_id"])->dec("balance", $school["deduction_num"])->update();
						}
						$account_log = ["wxapp_id" => $v["wxapp_id"], "bus_id" => $v["store_id"], "o_id" => $v["id"], "type" => 0, "number" => $school["deduction_num"]];
						$store_res4 = \think\facade\Db::name("business_account_log")->insert($account_log);
					}
					$brokerage = \floatval($v["total"]) - \floatval($v["s_money"]) - \floatval($v["fxs_money"]);
					$add_money = $add_money < 0 ? 0 : $add_money;
					$rider = \think\facade\Db::name("wechat_user")->where("wxapp_id", $v["wxapp_id"])->where("openid", $v["end_openid"])->find();
					$res2 = true;
					$res3 = true;
					$res4 = true;
					if ($add_money > 0) {
						$res2 = \think\facade\Db::name("wechat_user")->where("u_id", $rider["u_id"])->inc("balance", $add_money)->inc("brokerage", $brokerage)->update();
						$insert = ["wxapp_id" => $v["wxapp_id"], "uid" => $rider["u_id"], "type" => 1, "price" => $add_money, "operate" => 0, "remark" => "用户跑腿收入,订单编号" . $v["id"]];
						$res3 = \think\facade\Db::name("user_account_log")->insert($insert);
					}
					$res1 = \think\facade\Db::name("dmh_school_order")->where("id", $v["id"])->update($update);
					if ($res1 && $res2 && $res3 && $res4 && $store_res1 && $store_res2 && $store_res3 && $store_res4) {
						\think\facade\Db::commit();
						echo $v["id"] . "====";
					} else {
						throw new \Exception("操作失败");
					}
				} catch (\Exception $e) {
					echo "操作失败" . $v["id"] . "====";
				}
			}
		}
	}
	public function CancelOrder()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if ($wxapp_id) {
			$order = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("cancel_time", "<", time() - 3600)->where("status", 9)->select();
			foreach ($order as &$v) {
				file_put_contents("一方取消的自动取消.txt", " 订单号 " . $v["id"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
				if ($v["status"] == 9) {
					\think\facade\Db::startTrans();
					if ($v["type"] == 3) {
						Printer::cancel($v["id"]);
						if ($v["type"] == 3) {
							$goods = json_decode(html_entity_decode($v["goods_list"]), true);
							foreach ($goods as $k1 => $v1) {
								\think\facade\Db::name("zh_goods")->where("id", $v1["ids"])->inc("stock", $v1["nums"])->update();
							}
						}
					}
					if ($v["cancel_from"] == "user") {
						$data["status"] = 8;
						$plus = \think\facade\Db::name("dmh_school_order_plus_price")->where("o_id", $v["id"])->where("pay_status", 1)->where("is_refund", 0)->select()->toArray();
						if (count($plus) > 0) {
							try {
								$this->plusRefund($plus, $wxapp_id);
							} catch (\Exception $e) {
								return $this->ajaxReturn($this->errorCode, $e->getMessage());
							}
						}
						$data["end_openid"] = "";
						$data["cancel_time"] = "";
						$data["s_money"] = 0;
						$data["fxs_money"] = 0;
						$data["store_money"] = 0;
						$data["fx_store_money"] = 0;
						if ($v["t_money"] > 0) {
							try {
								\app\api\service\PaymentService::instance($wxapp_id)->refund($v["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $v["t_money"], $v["t_money"], $v["pay_type"]);
								if ($v["co_id"]) {
									\app\api\model\UserCoupon::where(["id" => $v["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
								}
								\app\api\model\Order::where(["id" => $v["id"]])->update($data);
								\think\facade\Db::commit();
							} catch (\Exception $e) {
								return $this->ajaxReturn($this->errorCode, $e->getMessage());
							}
						} else {
							if ($v["co_id"]) {
								\app\api\model\UserCoupon::where(["id" => $v["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
							}
							\app\api\model\Order::where(["id" => $v["id"]])->update($data);
							\think\facade\Db::commit();
						}
					} elseif ($v["cancel_from"] == "rider") {
						$update["status"] = 2;
						$update["end_openid"] = "";
						$update["cancel_time"] = "";
						$update["s_money"] = 0;
						$update["fxs_money"] = 0;
						$update["store_money"] = 0;
						$update["fx_store_money"] = 0;
						$res = \think\facade\Db::name("dmh_school_order")->where("id", $v["id"])->update($update);
					}
					if ($res) {
						\think\facade\Db::commit();
					}
				}
			}
		}
	}
	public function SecondAuto()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if ($wxapp_id) {
			$time = date("Y-m-d H:i:s", strtotime("-7day"));
			$order = \think\facade\Db::name("dmh_market_order")->where("wxapp_id", $wxapp_id)->where("create_time", "<", strtotime($time))->where("paystate", 1)->select();
			foreach ($order as &$v) {
				Distribution::index(3, $v["id"]);
				$publisher = \think\facade\Db::name("dmh_market_goods")->alias("g")->join("wechat_user u", "g.u_id = u.u_id")->where("m_id", $v["m_id"])->find();
				$add_money = $v["pay"] - ($v["commission"] + $v["branch"]);
				$insert = ["wxapp_id" => $v["wxapp_id"], "uid" => $publisher["u_id"], "type" => 2, "price" => $add_money, "operate" => 0, "remark" => "二手市场收入，订单号" . $v["id"]];
				\think\facade\Db::name("user_account_log")->insert($insert);
				\think\facade\Db::name("wechat_user")->where("u_id", $publisher["u_id"])->inc("balance", $add_money)->inc("brokerage", $add_money)->update();
				\think\facade\Db::name("dmh_market_order")->where("id", $v["id"])->update(["paystate" => 2]);
			}
		}
	}
	public function batches()
	{
		$wxapp_id = input("wxapp_id");
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$dataList = \think\facade\Db::name("user_withdraw")->where("wxapp_id", $wxapp_id)->where("status", 4)->order("id", "desc")->select();
		foreach ($dataList as &$v) {
			$url = "https://api.mch.weixin.qq.com/v3/transfer/batches/batch-id/" . $v["batch_id"] . "?need_query_detail=true&detail_status=ALL";
			$result = \app\accounts\controller\HttpExtend::get($url, "", ["headers" => \app\BaseController::apiv3sign($url, "GET", "", $config)]);
			$result = json_decode($result, true);
			if ($result["transfer_batch"]["batch_status"] == "FINISHED" && $result["transfer_detail_list"][0]["detail_status"] == "SUCCESS") {
				\think\facade\Db::name("user_withdraw")->where("batch_id", $v["batch_id"])->update(["status" => 2]);
			} else {
				if ($result["transfer_batch"]["batch_status"] == "FINISHED" && $result["transfer_detail_list"][0]["detail_status"] == "FAIL" || $result["transfer_batch"]["batch_status"] == "CLOSED") {
					\think\facade\Db::name("user_withdraw")->where("batch_id", $v["batch_id"])->update(["status" => 1]);
				}
			}
		}
	}
}