<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class UserWithdraw extends Admin
{
	public function index()
	{
		if (!$this->request->isAjax()) {
			$user = \think\facade\Db::name("user_withdraw")->where(["wxapp_id" => session("accounts.wxapp_id")])->group("u_id")->field("u_id")->select()->toArray();
			foreach ($user as &$v) {
				$v["nickname"] = \think\facade\Db::name("wechat_user")->where("u_id", $v["u_id"])->value("nickname");
			}
			$this->view->assign("user", $user);
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$where["account"] = $this->request->param("account", "", "serach_in");
			$where["name"] = $this->request->param("name_s", "", "serach_in");
			$where["type"] = $this->request->param("type", "", "serach_in");
			$where["status"] = $this->request->param("status", "", "serach_in");
			$where["u_id"] = $this->request->param("u_id", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "*";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$dataList = \think\facade\Db::name("user_withdraw")->where(["status" => 1, "type" => 2, "wxapp_id" => session("accounts.wxapp_id")])->select();
			foreach ($dataList as &$v) {
				if (!$v["out_detail_no"]) {
					$out_detail_no = date("YmdHis") . rand(10, 100) . $v["id"];
					\think\facade\Db::name("user_withdraw")->where("id", $v["id"])->update(["out_detail_no" => $out_detail_no]);
				}
			}
			$res = \app\accounts\service\UserWithdrawService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			foreach ($res["rows"] as &$v) {
				$v["nickname"] = \think\facade\Db::name("wechat_user")->where("u_id", $v["u_id"])->value("nickname");
			}
			return json($res);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$id = $this->request->get("id", "", "serach_in");
			if (!$id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\accounts\model\UserWithdraw::find($id)));
			return view("update");
		} else {
			$postField = "id,status";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$info = \think\facade\Db::name("user_withdraw")->find($data["id"]);
			if ($info["status"] != 1) {
				return json(["status" => "01", "msg" => "不可重复操作"]);
			}
			if ($info["status"] == $data["status"]) {
				return json(["status" => "01", "msg" => "未作修改"]);
			}
			\think\facade\Db::startTrans();
			try {
				if ($data["status"] == 2 && $info["status"] == 1) {
					if ($info["type"] == 1) {
						$where["wxapp_id"] = session("accounts.wxapp_id");
						$alipaydata = \think\facade\Db::name("setting")->where($where)->find();
						if ($alipaydata["is_alipay"] == 1) {
							$re = Withdrawalalipay::alipay($info);
							if ($re["code"] == 200) {
								$data["alipayorder_id"] = $re["order_id"];
								$res = \app\accounts\service\UserWithdrawService::update($data);
							} else {
								return json(["status" => "01", "msg" => $re["msg"]]);
							}
						} else {
							$res = \app\accounts\service\UserWithdrawService::update($data);
						}
					} else {
						$config = \think\facade\Db::name("setting")->where("wxapp_id", $info["wxapp_id"])->find();
						$user = \think\facade\Db::name("wechat_user")->find($info["u_id"]);
						if ($config["pay_method"] == 0) {
							$payresult = $this->paytouser($user["openid"], $info["price"], $config);
							if ($payresult["return_code"] != "SUCCESS" || $payresult["result_code"] != "SUCCESS") {
								file_put_contents("tixian.txt", " 提现id " . $info["id"] . "付款失败，原因" . $payresult["return_msg"] . "，时间" . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
								$res = false;
							} else {
								file_put_contents("tixian.txt", " 提现id " . $info["id"] . "付款成功，时间" . date("Y-m-d H:i:s") . " 微信付款单号" . $payresult["payment_no"] . PHP_EOL, FILE_APPEND);
								$data["payment_no"] = $payresult["payment_no"];
								$res = \app\accounts\service\UserWithdrawService::update($data);
							}
						} else {
							$payresult = $this->batches($user["openid"], $info["price"], $config, $info["out_detail_no"]);
							file_put_contents("sjzz.txt", " 提现id " . $info["id"] . "   " . json_encode($payresult) . "，时间" . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
							if ($payresult["batch_id"]) {
								$res = true;
								\think\facade\Db::name("user_withdraw")->where("id", $data["id"])->update(["batch_id" => $payresult["batch_id"], "out_batch_no" => $payresult["out_batch_no"], "status" => 4]);
							} else {
								$res = false;
								file_put_contents("sjzz.txt", " 提现id " . $info["id"] . "  失败原因 " . $payresult["message"] . "，时间" . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
							}
						}
					}
					if ($res) {
						\think\facade\Db::commit();
						return json(["status" => "00", "msg" => "操作成功"]);
					} else {
						throw new \Exception("操作失败");
					}
				} else {
					if ($data["status"] == 3) {
						if ($info["is_spread"] == 1) {
							$res1 = \think\facade\Db::name("spread_account_log")->insert(["wxapp_id" => $info["wxapp_id"], "s_id" => 0, "u_id" => $info["u_id"], "price" => $info["price"], "operate" => 1, "remark" => "提现拒绝"]);
						} else {
							$remark = $info["is_store"] == 1 ? "商家提现拒绝，金额回退" : "提现拒绝，金额回退";
							$log["wxapp_id"] = $info["wxapp_id"];
							$log["uid"] = $info["u_id"];
							$log["price"] = $info["price"];
							$log["type"] = $info["is_store"] == 1 ? 3 : 1;
							$log["operate"] = 0;
							$log["remark"] = $remark;
							$res1 = \think\facade\Db::name("user_account_log")->insert($log);
						}
						if ($info["is_store"] == 1) {
							$res2 = \think\facade\Db::name("wechat_user")->where("u_id", $info["u_id"])->inc("store_balance", $info["price"])->update();
						} elseif ($info["is_spread"] == 1) {
							$res2 = \think\facade\Db::name("wechat_user")->where("u_id", $info["u_id"])->inc("spread_balance", $info["price"])->update();
						} else {
							$res2 = \think\facade\Db::name("wechat_user")->where("u_id", $info["u_id"])->inc("balance", $info["price"])->update();
						}
						$res = \app\accounts\service\UserWithdrawService::update($data);
						if ($res && $res1 && $res2) {
							\think\facade\Db::commit();
							return json(["status" => "00", "msg" => "操作成功"]);
						} else {
							throw new \Exception("操作失败");
						}
					}
				}
			} catch (\Exception $e) {
				\think\facade\Db::rollback();
				return json(["status" => "01", "msg" => $e->getMessage()]);
			}
		}
	}
	public function paytouser($openid, $price, $config)
	{
		$url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
		$host_url = "http://" . $_SERVER["HTTP_HOST"];
		$path_cert = $config["refund_cert"];
		$path_key = $config["refund_key"];
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
		return $response;
	}
	public function batches($openid, $price, $config, $out_detail_no)
	{
		$url = "https://api.mch.weixin.qq.com/v3/transfer/batches";
		$transfer_detail_list[] = ["out_detail_no" => $out_detail_no, "transfer_amount" => intval(bcmul($price, 100)), "transfer_remark" => "用户提现", "openid" => $openid];
		$data = ["appid" => $config["appid"], "out_batch_no" => date("YmdHis") . rand(1000, 9999), "batch_name" => "商家转账", "batch_remark" => "商家转账", "total_amount" => intval(bcmul($price, 100)), "total_num" => 1, "transfer_detail_list" => $transfer_detail_list];
		$re = HttpExtend::post($url, json_encode($data), ["headers" => \app\BaseController::apiv3sign($url, "POST", json_encode($data), $config)]);
		$result = json_decode($re, true);
		return $result;
	}
}