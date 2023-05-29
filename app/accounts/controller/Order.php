<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Order extends Admin
{
	public function index()
	{
		if (!$this->request->isAjax()) {
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["delete_time"] = ["exp", "is null"];
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$where["ordersn"] = $this->request->param("ordersn", "", "serach_in");
			$where["type"] = $this->request->param("type", "", "serach_in");
			$where["status"] = $this->request->param("status", "", "serach_in");
			$where["sex_limit"] = $this->request->param("sex_limit", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,s_id,ordersn,type,status,sh_name,y_money,total,t_money,s_money,fxs_money,store_money,fx_store_money,createtime";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$school = \think\facade\Db::name("school")->where("wxapp_id", session("accounts.wxapp_id"))->select();
			foreach ($school as &$v) {
				$schoolList[$v["s_id"]] = $v["s_name"];
			}
			$res = \app\accounts\service\OrderService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			foreach ($res["rows"] as &$v) {
				$v["s_name"] = $schoolList[$v["s_id"]];
				$v["plus_price"] = \think\facade\Db::name("dmh_school_order_plus_price")->where("o_id", $v["id"])->where(["pay_status" => 1, "is_refund" => 0])->sum("price");
			}
			return json($res);
		}
	}
	public function dumpData()
	{
		$where = [];
		$where["wxapp_id"] = session("accounts.wxapp_id");
		$where["ordersn"] = $this->request->param("ordersn", "", "serach_in");
		$where["type"] = $this->request->param("type", "", "serach_in");
		$where["status"] = $this->request->param("status", "", "serach_in");
		$where["sex_limit"] = $this->request->param("sex_limit", "", "serach_in");
		$createtime_start = $this->request->param("createtime_start", "", "serach_in");
		$createtime_end = $this->request->param("createtime_end", "", "serach_in");
		$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
		$where["id"] = ["in", $this->request->param("id", "", "serach_in")];
		try {
			$fieldInfo = ["id", "express_info", "ordersn", "type", "status", "sh_name", "y_money", "total", "t_money", "createtime", "remarks", "sh_phone", "sh_addres", "food_money", "qu_addres", "s_money", "fxs_money", "store_money", "fx_store_money"];
			$school = \think\facade\Db::name("school")->where("wxapp_id", session("accounts.wxapp_id"))->select();
			foreach ($school as &$v) {
				$schoolList[$v["s_id"]] = $v["s_name"];
			}
			$list = \app\accounts\model\Order::where(formatWhere($where))->order("id desc")->select();
			$datas = [];
			foreach ($list as $k => $v) {
				$datas[$k] = $v;
				$express_info = json_decode($v["express_info"], true);
				if ($express_info) {
					$info = "";
					foreach ($express_info as $ks => $vs) {
						$code = $vs["code"] . ",";
						$info .= $code;
					}
					$datas[$k]["express_info"] = $info;
				}
				$business = $this->app->db->name("zh_business")->where("business_id", $v["store_id"])->find();
				$datas[$k]["business_name"] = $business["business_name"];
			}
			$list = $datas;
			if (empty($list)) {
				throw new Exception("没有数据");
			}
			\app\accounts\service\OrderService::dumpData(htmlOutList($list), filterEmptyArray(array_unique($fieldInfo)), $schoolList);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
	}
	public function hsz()
	{
		if (!$this->request->isAjax()) {
			return view("hsz");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["ordersn"] = $this->request->param("ordersn", "", "serach_in");
			$where["type"] = $this->request->param("type", "", "serach_in");
			$where["status"] = $this->request->param("status", "", "serach_in");
			$where["sex_limit"] = $this->request->param("sex_limit", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,ordersn,type,status,sh_name,y_money,total,t_money,createtime";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\accounts\service\OrderService::hszList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function resumeData()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			$data = \app\accounts\model\Order::onlyTrashed()->where(["id" => explode(",", $idx)])->select();
			foreach ($data as $v) {
				$v->restore();
			}
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function trashDelete()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\Order::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function view()
	{
		$id = $this->request->get("id", "", "serach_in");
		if (!$id) {
			$this->error("参数错误");
		}
		$info = \app\accounts\model\Order::find($id);
		$info["img"] = !empty($info["img"]) ? explode(",", $info["img"]) : "";
		$rider = \think\facade\Db::name("wechat_user")->where("wxapp_id", $info["wxapp_id"])->where("openid", $info["end_openid"])->find();
		$info["rider_name"] = $rider["t_name"];
		$info["rider_phone"] = $rider["phone"];
		$info["express_info"] = $info["express_info"] ? json_decode($info["express_info"], true) : "";
		$user = \think\facade\Db::name("wechat_user")->find($info["u_id"]);
		$info["nickname"] = $user["nickname"];
		$info["avatar"] = $user["avatar"];
		$this->view->assign("info", $info);
		return view("view");
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$id = $this->request->get("id", "", "serach_in");
			if (!$id) {
				$this->error("参数错误");
			}
			$order = \think\facade\Db::name("dmh_school_order")->where("id", $id)->find();
			$this->view->assign("info", $order);
			return view("update");
		} else {
			$postField = "id,status,ordersn";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$order = \think\facade\Db::name("dmh_school_order")->where("id", $data["id"])->find();
			if ($order["status"] == 1) {
				$json = ["status" => "01", "msg" => "订单未支付不允许修改"];
				return json($json);
			}
			if ($order["status"] == 4) {
				$json = ["status" => "01", "msg" => "订单已完成不允许修改"];
				return json($json);
			}
			if ($order["status"] == 8) {
				$json = ["status" => "01", "msg" => "订单已取消不允许修改"];
				return json($json);
			}
			$rest = $this->middleoperation($data["status"], $data);
			if ($rest["status"] != "00") {
				return json($rest);
			}
			$log = ["o_id" => $data["id"], "u_id" => session("accounts.account"), "wxapp_id" => session("accounts.wxapp_id"), "addtime" => time(), "status" => $data["status"]];
			$this->uplog($log);
			$json = ["status" => "00", "msg" => "操作成功"];
			return json($json);
		}
	}
	public function uplog($data)
	{
		\think\facade\Db::name("order_log")->insert($data);
	}
	public function middleoperation($status, $data)
	{
		$order = \think\facade\Db::name("dmh_school_order")->where("ordersn", $data["ordersn"])->find();
		$user = \think\facade\Db::name("wechat_user")->where("openid", $order["start_openid"])->find();
		$users = \think\facade\Db::name("wechat_user")->where("openid", $order["end_openid"])->find();
		if ($status == 4) {
			$order = \think\facade\Db::name("dmh_school_order")->where("ordersn", $data["ordersn"])->where(["end_openid" => null])->find();
			if ($order) {
				return ["status" => "01", "msg" => "订单没有接单员"];
			}
			$this->confirmFinish($data["id"], session("accounts.wxapp_id"), $users["u_id"]);
			return ["status" => "00", "msg" => "正确下一步"];
		} elseif ($status == 8) {
			$res = $this->userCancelOrder($data["id"], session("accounts.wxapp_id"), $user["u_id"]);
			return ["status" => "00", "msg" => "正确下一步"];
		} elseif ($status == 3 || $status == 7) {
			$order = \think\facade\Db::name("dmh_school_order")->where("ordersn", $data["ordersn"])->where(["end_openid" => null])->find();
			if ($order) {
				return ["status" => "01", "msg" => "订单没有接单员"];
			}
			\think\facade\Db::name("dmh_school_order")->where("ordersn", $data["ordersn"])->update(["status" => $data["status"]]);
			return ["status" => "00", "msg" => "正确下一步"];
		} elseif ($status == 2) {
			$res = \think\facade\Db::name("dmh_school_order")->where("ordersn", $data["ordersn"])->update(["end_openid" => null, "status" => 2]);
			return ["status" => "00", "msg" => "正确下一步"];
		} else {
			$order = \think\facade\Db::name("dmh_school_order")->where("ordersn", $data["ordersn"])->where(["end_openid" => null])->find();
			if ($order) {
				return ["status" => "01", "msg" => "订单没有接单员"];
			}
			\think\facade\Db::name("dmh_school_order")->where("ordersn", $data["ordersn"])->update(["status" => $data["status"]]);
			return ["status" => "00", "msg" => "其他状态下一步"];
		}
	}
	public function confirmFinish($o_id, $wxapp_id, $uid)
	{
		$order = \app\accounts\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		if (!$order) {
			return ["status" => "01", "msg" => "未找到订单"];
		}
		$user = \app\api\model\WechatUser::where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		\think\facade\Db::startTrans();
		try {
			$school = \think\facade\Db::name("school")->find($order["s_id"]);
			\app\api\controller\Distribution::index(2, $order["id"]);
			$res1 = \app\accounts\model\Order::where(["id" => $o_id])->update(["status" => "4"]);
			if ($order["is_open_pay"] == 1 && $order["type"] == 3) {
				$add_money = \floatval($order["total"]) - \floatval($order["s_money"]) - \floatval($order["fxs_money"]) + $order["food_money"];
			} else {
				$add_money = \floatval($order["total"]) - \floatval($order["s_money"]) - \floatval($order["fxs_money"]);
			}
			$store_res1 = true;
			$store_res2 = true;
			$store_res3 = true;
			$store_res4 = true;
			if ($order["is_open_pay"] == 0 && $order["type"] == 3) {
				$store = \think\facade\Db::name("zh_business")->find($order["store_id"]);
				$money = bcsub($order["food_money"], $order["store_money"], 2);
				$store_money = bcsub($money, $order["fx_store_money"], 2);
				if ($order["is_self_lifting"] != 1) {
					$store_money = bcsub($store_money, $store["deliveryfee"], 2);
				}
				file_put_contents("finish.txt", "订单号" . $o_id . "  商家佣金  " . $store_money . "  商家id  " . $order["store_id"] . "  商家管理员  " . $store["wxadmin_name"] . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
				if ($store_money > 0 && $store["wxadmin_name"] > 0) {
					$store_res1 = \app\api\model\WechatUser::where("u_id", $store["wxadmin_name"])->inc("store_balance", $store_money)->update();
					$store_res2 = \think\facade\Db::name("user_account_log")->insert(["wxapp_id" => $wxapp_id, "uid" => $store["wxadmin_name"], "price" => $store_money, "type" => 3, "operate" => 0, "remark" => "商家订单收入，订单号" . $o_id]);
				}
			}
			if ($school["store_bean_switch"] == 1 && $order["type"] == 3) {
				$school["deduction_num"] = $school["deduction_num"] ? $school["deduction_num"] : 0;
				if ($school["deduction_num"] > 0) {
					$store_res3 = \think\facade\Db::name("zh_business")->where("business_id", $order["store_id"])->dec("balance", $school["deduction_num"])->update();
				}
				$account_log = ["wxapp_id" => $order["wxapp_id"], "bus_id" => $order["store_id"], "o_id" => $order["id"], "type" => 0, "number" => $school["deduction_num"]];
				$store_res4 = \think\facade\Db::name("business_account_log")->insert($account_log);
			}
			$brokerage = \floatval($order["total"]) - \floatval($order["s_money"]) - \floatval($order["fxs_money"]);
			$add_money = $add_money < 0 ? 0 : $add_money;
			$res2 = true;
			$res3 = true;
			$res4 = true;
			if ($add_money > 0) {
				$res2 = \app\api\model\WechatUser::where("u_id", $user["u_id"])->inc("balance", $add_money)->update();
				$log["wxapp_id"] = $wxapp_id;
				$log["uid"] = $user["u_id"];
				$log["price"] = $add_money;
				$log["type"] = 1;
				$log["operate"] = 0;
				$log["remark"] = "用户跑腿收入，订单编号" . $o_id;
				$res3 = \think\facade\Db::name("user_account_log")->insert($log);
			}
			if ($brokerage > 0) {
				$res4 = \app\api\model\WechatUser::where("u_id", $user["u_id"])->inc("brokerage", $brokerage)->update();
			}
			if ($res1 && $res2 && $res3 && $res4 && $store_res1 && $store_res2 && $store_res3 && $store_res4) {
				\think\facade\Db::commit();
				return ["status" => "00", "msg" => "操作成功"];
			} else {
				throw new \Exception("操作失败");
			}
		} catch (\Exception $e) {
			return ["status" => "00", "msg" => $e->getMessage()];
		}
	}
	public function userCancelOrder($o_id, $wxapp_id, $uid)
	{
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		$order = \app\accounts\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id, "start_openid" => $user["openid"]])->find();
		\think\facade\Db::startTrans();
		if ($order["type"] == 3) {
			$goods = json_decode(html_entity_decode($order["goods_list"]), true);
			foreach ($goods as $k => $v) {
				\think\facade\Db::name("zh_goods")->where("id", $v["ids"])->inc("stock", $v["nums"])->update();
			}
		}
		if (!$order) {
			return ["status" => "01", "msg" => "未找到订单"];
		}
		$data = ["cancel_from" => "user", "cancel_time" => time()];
		if ($order["status"] == 1) {
			$data["status"] = 8;
			if ($order["co_id"]) {
				UserCoupon::where(["id" => $order["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
			}
			\app\accounts\model\Order::where(["id" => $order["id"]])->update($data);
			\think\facade\Db::commit();
			return ["status" => "00", "msg" => "取消成功"];
		}
		if ($order["status"] == 10) {
			$data["status"] = 8;
			if (strtotime("+30 minute") > strtotime(date("Y-m-d " . $order["arrival_time"]))) {
				return ["status" => "01", "msg" => "到店前30分钟内不可取消"];
			}
			try {
				\app\api\service\PaymentService::instance($wxapp_id)->refund($order["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $order["t_money"], $order["t_money"], $order["pay_type"]);
				\app\accounts\model\Order::where(["id" => $order["id"]])->update($data);
				\think\facade\Db::commit();
				return ["status" => "00", "msg" => "取消成功"];
			} catch (\Exception $e) {
				return ["status" => "01", "msg" => $e->getMessage()];
			}
		}
		$data["status"] = 8;
		$plus = \think\facade\Db::name("dmh_school_order_plus_price")->where("o_id", $order["id"])->where("pay_status", 1)->where("is_refund", 0)->select()->toArray();
		if (count($plus) > 0) {
			try {
				$this->plusRefund($plus, $wxapp_id);
			} catch (\Exception $e) {
				return ["status" => "01", "msg" => $e->getMessage()];
			}
		}
		if ($order["t_money"] > 0) {
			try {
				\app\api\service\PaymentService::instance($wxapp_id)->refund($order["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $order["t_money"], $order["t_money"], $order["pay_type"]);
				if ($order["co_id"]) {
					UserCoupon::where(["id" => $order["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
				}
				\app\accounts\model\Order::where(["id" => $order["id"]])->update($data);
				\think\facade\Db::commit();
			} catch (\Exception $e) {
				return ["status" => "01", "msg" => $e->getMessage()];
			}
		} else {
			if ($order["co_id"]) {
				UserCoupon::where(["id" => $order["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
			}
			\app\accounts\model\Order::where(["id" => $order["id"]])->update($data);
			\think\facade\Db::commit();
		}
		return ["status" => "00", "msg" => "取消成功"];
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
}