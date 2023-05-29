<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Order extends Common
{
	public function takeExpressOrder()
	{
		$postField = "a_id,unique,s_id,wxapp_id,total,img,remarks,sex_limit,weight,express_num,sh_addres,qu_addres,qu_longitude,qu_latitude,co_id,out_time_num,start_time,MinRunPrice,express_id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if ($data["unique"]) {
			$cache = \think\facade\Cache::get($data["unique"]);
			if ($cache) {
				$data["total"] = $cache["total"];
				$data["t_money"] = $cache["t_money"];
				$data["y_money"] = $cache["y_money"];
				$data["express_info"] = $cache["express_info"];
			}
		}
		if (!$cache) {
			return $this->ajaxReturn($this->errorCode, "订单未找到");
		}
		if (!valid($data, ["s_id", "wxapp_id"])) {
			return $this->ajaxReturn($this->errorCode, "参数错误");
		}
		$judge = $this->msg_check($data["remarks"], $data["wxapp_id"]);
		if (!$judge) {
			return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
		}
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where(["u_id" => $uid, "wxapp_id" => $data["wxapp_id"]])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		if (!valid($data, "a_id,qu_addres,out_time_num")) {
			return $this->ajaxReturn($this->errorCode, "请完善信息");
		}
		if (empty($data["start_time"])) {
			$this->ajaxReturn($this->errorCode, "送达时间不能为空");
		}
		$addres = \app\api\model\Address::where(["a_id" => $data["a_id"]])->find();
		if (empty($addres)) {
			return $this->ajaxReturn($this->errorCode, "收货地址有误");
		}
		if ($data["s_id"] != $addres["s_id"]) {
			return $this->ajaxReturn($this->errorCode, "收货学校与所选项学校不一致");
		}
		if ($data["total"] <= 0) {
			return $this->ajaxReturn($this->errorCode, "金额错误");
		}
		$start = explode(" ", $data["start_time"]);
		$start[0] = date("Y") . "-" . $start[0];
		if ($start[1] == "2小时内") {
			$data["start_time"] = strtotime("+2 hour", strtotime($start[0] . " " . date("H:i:s")));
		} elseif ($start[1] == "当天送达") {
			$data["start_time"] = strtotime($start[0] . " 23:59");
		} else {
			$data["start_time"] = strtotime(implode(" ", $start));
		}
		if ($data["start_time"] < strtotime("+10 minute")) {
			return $this->ajaxReturn($this->errorCode, "送达时间有误");
		}
		$site = \app\api\model\Setting::where("wxapp_id", $data["wxapp_id"])->find();
		$data["type"] = 1;
		$data["ordersn"] = date("YmdHis") . rand(1000, 9999);
		$data["start_openid"] = $user["openid"];
		$data["sh_name"] = $addres["name"];
		$data["sh_sex"] = $addres["sex"];
		$data["sh_phone"] = $addres["phone"];
		$data["sh_school"] = $addres["s_id"];
		$data["sh_addres"] = $addres["addres"];
		$data["createtime"] = time();
		$data["out_time"] = time() + $data["out_time_num"] * 60 * 60;
		$data["out_time_num"] = $data["start_time"];
		$data["u_id"] = $uid;
		$data["pay_type"] = $site["pay_type"];
		if ($data["co_id"]) {
			$coupon = \app\api\model\UserCoupon::where(["id" => $data["co_id"], "wxapp_id" => $data["wxapp_id"]])->find();
			if (!$coupon) {
				return $this->ajaxReturn($this->errorCode, "优惠券不可用");
			}
			if ($coupon["use_status"] == 1) {
				return $this->ajaxReturn($this->errorCode, "优惠券已使用");
			}
			$data["co_name"] = $coupon["c_name"];
		}
		$data["status"] = $data["t_money"] <= 0 ? 2 : 1;
		$o_id = \app\api\model\Order::insertGetId($data);
		$order = \app\api\model\Order::find($o_id);
		if ($data["co_id"]) {
			\app\api\model\UserCoupon::where("id", $data["co_id"])->update(["use_status" => 1, "update_time" => time()]);
		}
		if ($data["t_money"] == 0) {
			\think\facade\Db::name("dmh_school_order")->where("id", $o_id)->update(["status" => 2]);
			YlyPrinter::index($order);
			return $this->ajaxReturn($this->successCode, "下单成功，无需支付", ["order" => $order]);
		} else {
			try {
				$notify_url = "http://" . $this->request->host() . "/api/Order/payResult/wxapp_id/" . $data["wxapp_id"];
				$js_pay = \app\api\service\PaymentService::instance($data["wxapp_id"])->create($user["openid"], $order["ordersn"], $order["t_money"], "下单", $site["pay_type"], ["notify_url" => $notify_url]);
				return $this->ajaxReturn($this->successCode, "操作成功", ["order" => $order, "paydata" => $js_pay]);
			} catch (\Exception $e) {
				return $this->ajaxReturn($this->errorCode, $e->getMessage());
			}
		}
	}
	public function sendExpressOrder()
	{
		$postField = "a_id,unique,s_id,wxapp_id,total,img,remarks,sex_limit,weight,express_num,sh_addres,qu_addres,qu_longitude,qu_latitude,co_id,out_time_num,start_time,MinRunPrice";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if ($data["unique"]) {
			$cache = \think\facade\Cache::get($data["unique"]);
			if ($cache) {
				$data["total"] = $cache["total"];
				$data["t_money"] = $cache["t_money"];
				$data["y_money"] = $cache["y_money"];
				$data["express_info"] = $cache["express_info"];
			}
		}
		if (!$cache) {
			return $this->ajaxReturn($this->errorCode, "订单未找到");
		}
		if (!valid($data, ["s_id", "wxapp_id"])) {
			return $this->ajaxReturn($this->errorCode, "参数错误");
		}
		if ($data["remarks"]) {
			$judge = $this->msg_check($data["remarks"], $data["wxapp_id"]);
			if (!$judge) {
				return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
			}
		}
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where(["u_id" => $uid, "wxapp_id" => $data["wxapp_id"]])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		if (!valid($data, "a_id,sh_addres,out_time_num")) {
			return $this->ajaxReturn($this->errorCode, "请完善信息");
		}
		if (empty($data["start_time"])) {
			$this->ajaxReturn($this->errorCode, "请选择取件时间");
		}
		$addres = \app\api\model\Address::where(["a_id" => $data["a_id"]])->find();
		if (empty($addres)) {
			return $this->ajaxReturn($this->errorCode, "取货地址有误");
		}
		if ($data["s_id"] != $addres["s_id"]) {
			return $this->ajaxReturn($this->errorCode, "取货学校与所选项学校不一致");
		}
		if ($data["total"] <= 0) {
			return $this->ajaxReturn($this->errorCode, "金额错误");
		}
		$start = explode(" ", $data["start_time"]);
		$start[0] = date("Y") . "-" . $start[0];
		if ($start[1] == "2小时内") {
			$data["start_time"] = strtotime("+2 hour", strtotime($start[0] . " " . date("H:i:s")));
		} elseif ($start[1] == "当天送达") {
			$data["start_time"] = strtotime($start[0] . " 23:59");
		} else {
			$data["start_time"] = strtotime(implode(" ", $start));
		}
		if ($data["start_time"] < strtotime("+10 minute")) {
			return $this->ajaxReturn($this->errorCode, "取货时间有误");
		}
		$site = \app\api\model\Setting::where("wxapp_id", $data["wxapp_id"])->find();
		$data["type"] = 2;
		$data["ordersn"] = date("YmdHis") . rand(1000, 9999);
		$data["start_openid"] = $user["openid"];
		$data["sh_school"] = $addres["s_id"];
		$data["qu_name"] = $addres["name"];
		$data["qu_sex"] = $addres["sex"];
		$data["qu_phone"] = $addres["phone"];
		$data["qu_addres"] = $addres["addres"];
		$data["createtime"] = time();
		$data["out_time"] = time() + $data["out_time_num"] * 60 * 60;
		$data["out_time_num"] = $data["start_time"];
		$data["u_id"] = $uid;
		$data["pay_type"] = $site["pay_type"];
		if ($data["co_id"]) {
			$coupon = \app\api\model\UserCoupon::where(["id" => $data["co_id"], "wxapp_id" => $data["wxapp_id"]])->find();
			if (!$coupon) {
				return $this->ajaxReturn($this->errorCode, "优惠券不可用");
			}
			if ($coupon["use_status"] == 1) {
				return $this->ajaxReturn($this->errorCode, "优惠券已使用");
			}
			$data["co_name"] = $coupon["c_name"];
		}
		$data["status"] = $data["t_money"] <= 0 ? 2 : 1;
		$o_id = \app\api\model\Order::insertGetId($data);
		$order = \app\api\model\Order::find($o_id);
		if ($data["co_id"]) {
			\app\api\model\UserCoupon::where("id", $data["co_id"])->update(["use_status" => 1, "update_time" => time()]);
		}
		if ($data["t_money"] == 0) {
			\think\facade\Db::name("dmh_school_order")->where("id", $o_id)->update(["status" => 2]);
			return $this->ajaxReturn($this->successCode, "下单成功，无需支付", ["order" => $order]);
		} else {
			try {
				$notify_url = "http://" . $this->request->host() . "/api/Order/payResult/wxapp_id/" . $data["wxapp_id"];
				$js_pay = \app\api\service\PaymentService::instance($data["wxapp_id"])->create($user["openid"], $order["ordersn"], $order["t_money"], "下单", $site["pay_type"], ["notify_url" => $notify_url]);
				return $this->ajaxReturn($this->successCode, "操作成功", ["order" => $order, "paydata" => $js_pay]);
			} catch (\Exception $e) {
				return $this->ajaxReturn($this->errorCode, $e->getMessage());
			}
		}
	}
	public function helpBuyOrder()
	{
		$postField = "a_id,givetype,ordertime,unique,s_id,wxapp_id,total,img,remarks,sex_limit,weight,express_num,sh_addres,qu_longitude,qu_latitude,co_id,out_time_num,start_time,good_details,food_money,store_id,MinRunPrice,goods_list,arrival_time,is_self_lifting";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if ($data["unique"]) {
			$cache = \think\facade\Cache::get($data["unique"]);
			if ($cache) {
				$data["total"] = $cache["total"];
				$data["t_money"] = $cache["t_money"];
				$data["food_money"] = $cache["food_money"];
				$data["y_money"] = $cache["y_money"];
			}
		}
		if (!$cache) {
			return $this->ajaxReturn($this->errorCode, "订单未找到");
		}
		$store = \think\facade\Db::name("zh_business")->find($data["store_id"]);
		$school = \think\facade\Db::name("school")->find($data["s_id"]);
		if ($data["is_self_lifting"] == 1) {
			$data["is_open_pay"] = 0;
			$data["is_store_delivery"] = 2;
		} else {
			$data["is_open_pay"] = $school["is_open_pay"];
			if ($school["is_open_pay"] == 0) {
				$data["is_store_delivery"] = $store["method"];
			}
		}
		if (!$store) {
			return $this->ajaxReturn($this->errorCode, "商家不存在");
		}
		$a = $data["goods_list"];
		$json = json_decode(html_entity_decode($a), true);
		$total_prices = 0;
		\think\facade\Db::startTrans();
		foreach ($json as $k => $v) {
			$information = \think\facade\Db::name("zh_goods")->alias("a")->where(["a.wxapp_id" => $data["wxapp_id"], "a.s_id" => $data["s_id"], "a.id" => $v["ids"]])->find();
			if ($information["stock"] < $v["nums"]) {
				return $this->ajaxReturn($this->errorCode, "商品库存不足");
			}
			\think\facade\Db::name("zh_goods")->where("id", $v["ids"])->dec("stock", $v["nums"])->update();
			$specs = json_decode(html_entity_decode($information["specs"]), true);
			if ($v["specs"] && count($specs["list"]) > 0) {
				foreach ($specs["list"] as &$v1) {
					if ($v["specs"] == $v1["type"]) {
						$total_prices += $v["nums"] * $v1["price"];
					}
				}
			} else {
				$total_prices += $v["nums"] * $information["price"];
			}
		}
		$data["box_fee"] = $store["box_fee"];
		$data["service_price"] = $store["service_price"];
		if (round($data["food_money"], 2) != round($total_prices + $store["box_fee"] + $store["service_price"], 2)) {
			return $this->ajaxReturn($this->errorCode, "商品金额有误" . $data["food_money"]);
		}
		if (!valid($data, ["s_id", "wxapp_id", "store_id"])) {
			return $this->ajaxReturn($this->errorCode, "参数错误");
		}
		if ($data["remarks"]) {
			$judge = $this->msg_check($data["remarks"], $data["wxapp_id"]);
			if (!$judge) {
				return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
			}
		}
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where(["u_id" => $uid, "wxapp_id" => $data["wxapp_id"]])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		if (!valid($data, "a_id,good_details,food_money,out_time_num")) {
			return $this->ajaxReturn($this->errorCode, "请完善信息");
		}
		if ($data["givetype"] == 1) {
			$ordertime = explode("-", $data["ordertime"]);
			$start_time = date("m-d", time()) . " " . $ordertime[1];
			$times = time();
			$start = strtotime($ordertime[0]);
			if ($start < $times) {
				return $this->ajaxReturn($this->errorCode, "请选择下一时间段点餐");
			}
			$data["start_time"] = $start_time;
		}
		if (empty($data["start_time"])) {
			$this->ajaxReturn($this->errorCode, "请选择送达时间");
		}
		$addres = \app\api\model\Address::where(["a_id" => $data["a_id"]])->find();
		if (empty($addres)) {
			return $this->ajaxReturn($this->errorCode, "取货地址有误");
		}
		if ($data["s_id"] != $addres["s_id"]) {
			return $this->ajaxReturn($this->errorCode, "取货学校与所选项学校不一致");
		}
		if ($data["total"] < 0) {
			return $this->ajaxReturn($this->errorCode, "金额错误");
		}
		if ($data["food_money"] <= 0) {
			return $this->ajaxReturn($this->errorCode, "商品费用有误");
		}
		$start = explode(" ", $data["start_time"]);
		$start[0] = date("Y") . "-" . $start[0];
		if ($start[1] == "2小时内") {
			$data["start_time"] = strtotime("+2 hour", strtotime($start[0] . " " . date("H:i:s")));
		} elseif ($start[1] == "当天送达") {
			$data["start_time"] = strtotime($start[0] . " 23:59");
		} elseif ($start[1] == "立即送出") {
			$data["start_time"] = strtotime("+30 minute", strtotime($start[0] . " " . date("H:i:s")));
		} else {
			$data["start_time"] = strtotime(implode(" ", $start));
		}
		if ($data["start_time"] < strtotime("+10 minute")) {
			return $this->ajaxReturn($this->errorCode, "取货时间有误");
		}
		$site = \app\api\model\Setting::where("wxapp_id", $data["wxapp_id"])->find();
		$where[] = ["createtime", "between", [strtotime(date("Y-m-d")), strtotime(date("Y-m-d", strtotime("+1 day")))]];
		$where[] = ["store_id", "=", $data["store_id"]];
		$count = \think\facade\Db::name("dmh_school_order")->where($where)->count();
		$data["pick_number"] = $count + 1;
		$data["type"] = 3;
		$data["ordersn"] = date("YmdHis") . rand(1000, 9999);
		$data["start_openid"] = $user["openid"];
		$data["sh_name"] = $addres["name"];
		$data["sh_sex"] = $addres["sex"];
		$data["sh_school"] = $addres["s_id"];
		$data["sh_phone"] = $addres["phone"];
		$data["sh_addres"] = $addres["addres"];
		$data["createtime"] = time();
		if ($data["givetype"] == 1) {
			$data["out_time"] = $data["start_time"] + $data["out_time_num"] * 60 * 60;
		} else {
			$data["out_time"] = time() + $data["out_time_num"] * 60 * 60;
		}
		$data["u_id"] = $uid;
		$data["pay_type"] = $site["pay_type"];
		if ($data["co_id"]) {
			$coupon = \app\api\model\UserCoupon::where(["id" => $data["co_id"], "wxapp_id" => $data["wxapp_id"]])->find();
			if (!$coupon) {
				return $this->ajaxReturn($this->errorCode, "优惠券不可用");
			}
			if ($coupon["use_status"] == 1) {
				return $this->ajaxReturn($this->errorCode, "优惠券已使用");
			}
			$data["co_name"] = $coupon["c_name"];
		}
		$o_id = \app\api\service\OrderService::helpBuyOrder($data);
		$order = \app\api\model\Order::find($o_id);
		\think\facade\Db::commit();
		if ($data["co_id"]) {
			\app\api\model\UserCoupon::where("id", $data["co_id"])->update(["use_status" => 1, "update_time" => time()]);
		}
		if ($data["t_money"] == 0) {
			\think\facade\Db::name("dmh_school_order")->where("id", $o_id)->update(["status" => 2]);
			return $this->ajaxReturn($this->successCode, "下单成功，无需支付", ["order" => $order]);
		} else {
			try {
				$notify_url = "http://" . $this->request->host() . "/api/Order/payResult/wxapp_id/" . $data["wxapp_id"];
				$js_pay = \app\api\service\PaymentService::instance($data["wxapp_id"])->create($user["openid"], $order["ordersn"], $order["t_money"], "下单", $site["pay_type"], ["notify_url" => $notify_url]);
				return $this->ajaxReturn($this->successCode, "操作成功", ["order" => $order, "paydata" => $js_pay]);
			} catch (\Exception $e) {
				return $this->ajaxReturn($this->errorCode, $e->getMessage());
			}
		}
	}
	public function universalOrder()
	{
		$postField = "a_id,unique,s_id,wxapp_id,total,title,is_start_show,img,remarks,sex_limit,co_id,out_time_num,start_time,attach_file,service_num,address_must,address_must,address_show,MinRunPrice";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if ($data["unique"]) {
			$cache = \think\facade\Cache::get($data["unique"]);
			if ($cache) {
				$data["total"] = $cache["total"];
				$data["t_money"] = $cache["t_money"];
				$data["y_money"] = $cache["y_money"];
			}
		}
		if (!$cache) {
			return $this->ajaxReturn($this->errorCode, "订单未找到");
		}
		if (!valid($data, ["s_id", "wxapp_id"])) {
			return $this->ajaxReturn($this->errorCode, "参数错误");
		}
		if ($data["remarks"]) {
			$judge = $this->msg_check($data["remarks"], $data["wxapp_id"]);
			if (!$judge) {
				return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
			}
		}
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where(["u_id" => $uid, "wxapp_id" => $data["wxapp_id"]])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		if (!valid($data, "title,out_time_num")) {
			return $this->ajaxReturn($this->errorCode, "请完善信息");
		}
		if (empty($data["start_time"])) {
			$this->ajaxReturn($this->errorCode, "请选择服务时间");
		}
		$addres = [];
		if ($data["address_must"] && $data["address_show"]) {
			$addres = \app\api\model\Address::where(["a_id" => $data["a_id"]])->find();
			if (empty($addres)) {
				return $this->ajaxReturn($this->errorCode, "地址有误");
			}
			if ($data["s_id"] != $addres["s_id"]) {
				return $this->ajaxReturn($this->errorCode, "取货学校与所选项学校不一致");
			}
		}
		if ($data["total"] <= 0) {
			return $this->ajaxReturn($this->errorCode, "金额错误");
		}
		$start = explode(" ", $data["start_time"]);
		$start[0] = date("Y") . "-" . $start[0];
		if ($start[1] == "2小时内") {
			$data["start_time"] = strtotime("+2 hour", strtotime($start[0] . " " . date("H:i:s")));
		} elseif ($start[1] == "当天送达") {
			$data["start_time"] = strtotime($start[0] . " 23:59");
		} else {
			$data["start_time"] = strtotime(implode(" ", $start));
		}
		if ($data["start_time"] < strtotime("+10 minute")) {
			return $this->ajaxReturn($this->errorCode, "取货时间有误");
		}
		$site = \app\api\model\Setting::where("wxapp_id", $data["wxapp_id"])->find();
		$data["type"] = 4;
		$data["ordersn"] = date("YmdHis") . rand(1000, 9999);
		$data["start_openid"] = $user["openid"];
		$data["sh_name"] = $addres["name"] ?: "";
		$data["sh_sex"] = $addres["sex"] ?: "";
		$data["sh_phone"] = $addres["phone"] ?: "";
		$data["sh_addres"] = $addres["addres"] ?: "";
		$data["sh_school"] = $addres["s_id"] ?: "";
		$data["createtime"] = time();
		$data["out_time"] = time() + $data["out_time_num"] * 60 * 60;
		$data["u_id"] = $uid;
		$data["pay_type"] = $site["pay_type"];
		if ($data["co_id"]) {
			$coupon = \app\api\model\UserCoupon::where(["id" => $data["co_id"], "wxapp_id" => $data["wxapp_id"]])->find();
			if (!$coupon) {
				return $this->ajaxReturn($this->errorCode, "优惠券不可用");
			}
			if ($coupon["use_status"] == 1) {
				return $this->ajaxReturn($this->errorCode, "优惠券已使用");
			}
			$data["co_name"] = $coupon["c_name"];
		}
		$o_id = \app\api\service\OrderService::universalOrder($data);
		$order = \app\api\model\Order::find($o_id);
		if ($data["co_id"]) {
			\app\api\model\UserCoupon::where("id", $data["co_id"])->update(["use_status" => 1, "update_time" => time()]);
		}
		if ($data["t_money"] <= 0) {
			\think\facade\Db::name("dmh_school_order")->where("id", $o_id)->update(["status" => 2]);
			return $this->ajaxReturn($this->successCode, "下单成功，无需支付", ["order" => $order]);
		} else {
			try {
				$notify_url = "http://" . $this->request->host() . "/api/Order/payResult/wxapp_id/" . $data["wxapp_id"];
				$js_pay = \app\api\service\PaymentService::instance($data["wxapp_id"])->create($user["openid"], $order["ordersn"], $order["t_money"], "下单", $site["pay_type"], ["notify_url" => $notify_url]);
				return $this->ajaxReturn($this->successCode, "操作成功", ["order" => $order, "paydata" => $js_pay]);
			} catch (\Exception $e) {
				return $this->ajaxReturn($this->errorCode, $e->getMessage());
			}
		}
	}
	public function orderLst()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$wxapp_id = $this->request->get("wxapp_id", "", "intval");
		if (empty($wxapp_id)) {
			return $this->ajaxReturn($this->errorCode, "参数错误");
		}
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		$where = ["start_openid" => $user["openid"], "wxapp_id" => $wxapp_id];
		$where["status"] = $this->request->get("status", "", "serach_in");
		$where["ordersn"] = $this->request->get("ordersn", "", "serach_in");
		$where["type"] = $this->request->get("type", "", "serach_in");
		$where["delete_time"] = ["exp", "is null"];
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\OrderService::orderLstList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as &$v) {
			$v["plus_price"] = 0;
			if ($v["_status_txt"] == "已取消") {
				$v["plus_price"] = \think\facade\Db::name("dmh_school_order_plus_price")->where("o_id", $v["id"])->where("pay_status", 1)->sum("price");
			}
		}
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function runOrderLst()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$wxapp_id = $this->request->get("wxapp_id", "", "intval");
		$s_id = $this->request->get("s_id", "", "intval");
		if (empty($wxapp_id)) {
			return $this->ajaxReturn($this->errorCode, "参数错误");
		}
		$where = ["status" => 2, "wxapp_id" => $wxapp_id, "s_id" => $s_id];
		$where["ordersn"] = $this->request->get("ordersn", "", "serach_in");
		$where["type"] = $this->request->get("type", "", "serach_in");
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\OrderService::orderLstList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function myOrderLst()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$wxapp_id = $this->request->get("wxapp_id", "", "intval");
		if (empty($wxapp_id)) {
			return $this->ajaxReturn($this->errorCode, "参数错误");
		}
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		$where = ["end_openid" => $user["openid"], "wxapp_id" => $wxapp_id];
		$where["status"] = $this->request->get("status", "", "serach_in");
		$where["ordersn"] = $this->request->get("ordersn", "", "serach_in");
		$where["type"] = $this->request->get("type", "", "serach_in");
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\OrderService::orderLstList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function payResult()
	{
		$wxapp_id = input("wxapp_id");
		$pay_type = input("pay_type");
		trace("进入回调" . $wxapp_id, "notify_" . date("Y_m_d"));
		if ($pay_type == 1) {
			$postStr = file_get_contents("php://input");
			$getData = json_decode($postStr, true);
			if ($getData["tradeStatus"] = "SUCCESS") {
				$order = \app\api\model\Order::where(["ordersn" => $getData["outTradeNo"]])->find();
				trace("进入回调7887" . $order["wxapp_id"] . "==" . $getData["outTradeNo"], "notify_" . date("Y_m_d"));
				if (!$order || $order->status == 2) {
					return true;
				}
				if ($order["status"] == 1) {
					$data = ["status" => 2, "pay_time" => time()];
					file_put_contents("order.txt", $getData["outTradeNo"] . "   " . $order->status . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
					if ($order["type"] == 1) {
						YlyPrinter::index($order);
					}
					\app\api\model\Order::where(["ordersn" => $getData["outTradeNo"]])->update($data);
					if ($order["is_store_delivery"] == 1) {
						MsgSubscribe::toAllRider($getData["outTradeNo"], $order["wxapp_id"]);
					} else {
						$this->StorePrint($order["id"], $order["wxapp_id"]);
					}
				}
			}
		} else {
			$app = \app\api\service\PaymentService::instance($wxapp_id)::getPayApp();
			$response = $app->handlePaidNotify(function ($message, $fail) {
				trace($message, "notify_" . date("Y_m_d"));
				$order = \app\api\model\Order::where(["ordersn" => $message["out_trade_no"]])->find();
				trace("进入回调7887" . $order["wxapp_id"] . "==" . $message["out_trade_no"], "notify_" . date("Y_m_d"));
				if (!$order || $order->status == 2) {
					return true;
				}
				if ($message["return_code"] === "SUCCESS") {
					if ($message["result_code"] === "SUCCESS") {
						$data = ["status" => 2, "pay_time" => time()];
						\app\api\model\Order::where(["ordersn" => $message["out_trade_no"]])->update($data);
						if ($order["type"] == 1) {
							YlyPrinter::index($order);
						}
						file_put_contents("order.txt", $message["out_trade_no"] . "   " . $order->status . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
						if ($order["is_store_delivery"] == 1) {
							MsgSubscribe::toAllRider($message["out_trade_no"], $order["wxapp_id"]);
						} else {
							$this->StorePrint($order["id"], $order["wxapp_id"]);
						}
						return true;
					}
				} else {
					return $fail("通信失败，请稍后再通知我");
				}
			});
			return $response->send();
		}
	}
	public function userCancelOrder()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id, "start_openid" => $user["openid"]])->find();
		\think\facade\Db::startTrans();
		if ($order["type"] == 3) {
			$goods = json_decode(html_entity_decode($order["goods_list"]), true);
			foreach ($goods as $k => $v) {
				\think\facade\Db::name("zh_goods")->where("id", $v["ids"])->inc("stock", $v["nums"])->update();
			}
		}
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		if (!in_array($order["status"], [1, 2, 3])) {
			return $this->ajaxReturn($this->errorCode, "订单不可取消");
		}
		$data = ["cancel_from" => "user", "cancel_time" => time()];
		if ($order["status"] == 1) {
			$data["status"] = 8;
			if ($order["co_id"]) {
				\app\api\model\UserCoupon::where(["id" => $order["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
			}
			\app\api\model\Order::where(["id" => $order["id"]])->update($data);
			\think\facade\Db::commit();
			return $this->ajaxReturn($this->successCode, "取消成功");
		}
		if ($order["status"] == 2) {
			$data["status"] = 8;
			$plus = \think\facade\Db::name("dmh_school_order_plus_price")->where("o_id", $order["id"])->where("pay_status", 1)->where("is_refund", 0)->select()->toArray();
			if (count($plus) > 0) {
				try {
					$this->plusRefund($plus, $wxapp_id);
				} catch (\Exception $e) {
					return $this->ajaxReturn($this->errorCode, $e->getMessage());
				}
			}
			if ($order["t_money"] > 0) {
				try {
					\app\api\service\PaymentService::instance($wxapp_id)->refund($order["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $order["t_money"], $order["t_money"], $order["pay_type"]);
					if ($order["co_id"]) {
						\app\api\model\UserCoupon::where(["id" => $order["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
					}
					\app\api\model\Order::where(["id" => $order["id"]])->update($data);
					\think\facade\Db::commit();
				} catch (\Exception $e) {
					return $this->ajaxReturn($this->errorCode, $e->getMessage());
				}
			} else {
				if ($order["co_id"]) {
					\app\api\model\UserCoupon::where(["id" => $order["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
				}
				\app\api\model\Order::where(["id" => $order["id"]])->update($data);
				\think\facade\Db::commit();
			}
			return $this->ajaxReturn($this->successCode, "取消成功");
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
	public function orderInfo()
	{
		$o_id = $this->request->get("o_id");
		$wxapp_id = $this->request->get("wxapp_id");
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		if (strpos($order["attach_file"], "http://") !== false) {
			$order["attach_file"] = str_replace("http://", "https://", $order["attach_file"]);
		}
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		$order["is_watch"] = $user && ($user["openid"] == $order["start_openid"] || $user["openid"] == $order["end_openid"]) ? true : false;
		$order["user_type"] = $user && $user["openid"] == $order["start_openid"] ? true : false;
		$order["express_info"] = $order["express_info"] ? json_decode($order["express_info"], true) : "";
		if ($order["is_open_pay"] == 1 && $order["type"] == 3) {
			$add_money = \floatval($order["total"]) - \floatval($order["s_money"]) - \floatval($order["fxs_money"]) + $order["food_money"];
		} else {
			$add_money = \floatval($order["total"]) - \floatval($order["s_money"]) - \floatval($order["fxs_money"]);
		}
		$order["actual_income"] = sprintf("%01.2f", $add_money);
		$order = \app\api\service\OrderService::statusWhere($order);
		if ($order["end_openid"]) {
			$order["rider_uid"] = \think\facade\Db::name("wechat_user")->where(["openid" => $order["end_openid"], "wxapp_id" => $order["wxapp_id"]])->value("u_id");
			$order["watch_user"] = $uid == $order["u_id"] ? "buyer" : "rider";
		}
		return $this->ajaxReturn($this->successCode, "订单详情", $order);
	}
	public function pay()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id, "start_openid" => $user["openid"]])->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		if ($order["status"] != 1) {
			return $this->ajaxReturn($this->errorCode, "不可支付");
		}
		if ($order["createtime"] + 1800 < time()) {
			return $this->ajaxReturn($this->errorCode, "超时30分钟，订单已失效");
		}
		$notify_url = "http://" . $this->request->host() . "/api/Order/payResult/wxapp_id/" . $wxapp_id;
		$site = \app\api\model\Setting::where("wxapp_id", $wxapp_id)->find();
		try {
			$js_pay = \app\api\service\PaymentService::instance($wxapp_id)->create($user["openid"], $order["ordersn"], $order["t_money"], "下单", $site["pay_type"], ["notify_url" => $notify_url]);
			return $this->ajaxReturn($this->successCode, "操作成功", ["order" => $order, "paydata" => $js_pay]);
		} catch (\Exception $e) {
			return $this->ajaxReturn($this->errorCode, $e->getMessage());
		}
	}
	public function del()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id, "start_openid" => $user["openid"]])->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		if (!in_array($order["status"], [1, 5, 8])) {
			return $this->ajaxReturn($this->errorCode, "订单不可删");
		}
		\app\api\model\Order::where(["id" => $order["id"]])->update(["delete_time" => date("Y-m-d H:i:s")]);
		return $this->ajaxReturn($this->successCode, "删除成功");
	}
	public function grab()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$set = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		if ($user["openid"] == $order["start_openid"]) {
			return $this->ajaxReturn($this->errorCode, "不可抢自己的单子");
		}
		if ($order["status"] != 2) {
			return $this->ajaxReturn($this->errorCode, "订单不可抢");
		}
		if ($order["sex_limit"] > 0) {
			if ($order["sex_limit"] != $user["t_sex"]) {
				return $this->ajaxReturn($this->errorCode, "与订单要求性别不符");
			}
		}
		if ($user["run_status"] != 2) {
			return $this->ajaxReturn($this->errorCode, "您还未成为骑手");
		}
		if ($set["take_all_switch"] == 0) {
			if ($user["auth_sid"] != $order["s_id"]) {
				return $this->ajaxReturn($this->errorCode, "不可抢外校单子");
			}
		}
		if (!empty($order["end_openid"])) {
			\think\facade\Db::name("dmh_school_order")->where(["id" => $o_id, "status" => 2])->update(["status" => 3]);
			return $this->ajaxReturn($this->errorCode, "来晚了，该订单被抢了。");
		}
		$school = \think\facade\Db::name("school")->find($order["s_id"]);
		$plat_rate = \floatval($order["total"]) * \floatval($school["plat_rate"]) / 100;
		$school_rate = \floatval($order["total"]) * \floatval($school["school_rate"]) / 100;
		if ($order["is_open_pay"] == 1 && $order["type"] == 3) {
			\app\api\model\Order::where(["id" => $o_id, "status" => 2])->update(["end_openid" => $user["openid"], "status" => 3, "s_money" => $plat_rate, "fxs_money" => $school_rate, "store_money" => 0, "fx_store_money" => 0]);
		} else {
			$store = \think\facade\Db::name("zh_business")->find($order["store_id"]);
			$store_rate1 = $store["store_money"] != null ? $store["store_money"] : $school["store_rate"];
			$fx_store_rate1 = $store["fx_store_money"] != null ? $store["fx_store_money"] : $school["fx_store_rate"];
			$store_rate = \floatval($order["food_money"]) * \floatval($store_rate1) / 100;
			$fx_store_rate = \floatval($order["food_money"]) * \floatval($fx_store_rate1) / 100;
			\app\api\model\Order::where(["id" => $o_id, "status" => 2])->update(["end_openid" => $user["openid"], "status" => 3, "s_money" => $plat_rate, "fxs_money" => $school_rate, "store_money" => $store_rate, "fx_store_money" => $fx_store_rate]);
		}
		\think\facade\Log::write(\think\facade\Db::name("dmh_school_order")->getLastSql(), "这是抢单的sql");
		if ($order["type"] == 3) {
			$store = \think\facade\Db::name("zh_business")->find($order["store_id"]);
			if ($store["printtype"] == 2) {
				Printyilianyun::callPrint($o_id, $order["store_id"]);
			} else {
				Printer::index($o_id);
			}
			if ($set["mp_template_store"]) {
				MpMsgSubscribe::toStore($o_id, $wxapp_id);
			}
			if ($set["template_store"]) {
				MsgSubscribe::toStore($o_id, $wxapp_id);
			}
		}
		if ($set["mp_template_grab"]) {
			MpMsgSubscribe::toPublisher($o_id, $wxapp_id);
		}
		if ($set["template_grab"]) {
			MsgSubscribe::toPublisher($o_id, $wxapp_id);
		}
		return $this->ajaxReturn($this->successCode, "抢单成功");
	}
	public function StorePrint($o_id, $wxapp_id)
	{
		$o_id = $o_id ? $o_id : $this->request->post("o_id");
		$wxapp_id = $wxapp_id ? $wxapp_id : $this->request->post("wxapp_id");
		$set = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		$store = \think\facade\Db::name("zh_business")->find($order["store_id"]);
		$user = \app\api\model\WechatUser::where("u_id", $store["wxadmin_name"])->find();
		$school = \think\facade\Db::name("school")->find($order["s_id"]);
		$plat_rate = \floatval($order["total"]) * \floatval($school["plat_rate"]) / 100;
		$school_rate = \floatval($order["total"]) * \floatval($school["school_rate"]) / 100;
		if ($order["is_open_pay"] == 1) {
			$res = \app\api\model\Order::where(["id" => $o_id])->update(["end_openid" => $user["openid"], "status" => 3, "s_money" => $plat_rate, "fxs_money" => $school_rate, "store_money" => 0, "fx_store_money" => 0]);
		} else {
			$store_rate1 = $store["store_money"] != null ? $store["store_money"] : $school["store_rate"];
			$fx_store_rate1 = $store["fx_store_money"] != null ? $store["fx_store_money"] : $school["fx_store_rate"];
			$store_rate = \floatval($order["food_money"]) * \floatval($store_rate1) / 100;
			$fx_store_rate = \floatval($order["food_money"]) * \floatval($fx_store_rate1) / 100;
			$res = \app\api\model\Order::where(["id" => $o_id])->update(["end_openid" => $user["openid"], "status" => 3, "s_money" => $plat_rate, "fxs_money" => $school_rate, "store_money" => $store_rate, "fx_store_money" => $fx_store_rate]);
		}
		if ($store["printtype"] == 2) {
			Printyilianyun::callPrint($o_id, $order["store_id"]);
		} else {
			Printer::index($o_id);
		}
		if ($set["mp_template_store"]) {
			MpMsgSubscribe::toStore($o_id, $wxapp_id);
		}
		if ($set["template_store"]) {
			MsgSubscribe::toStore($o_id, $wxapp_id);
		}
		if ($res) {
			return $this->ajaxReturn($this->successCode, "操作成功");
		} else {
			return $this->ajaxReturn($this->errorCode, "操作失败");
		}
	}
	public function ConfirmPickup()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		if ($order["status"] != 3) {
			return $this->ajaxReturn($this->errorCode, "订单不是待取货状态");
		}
		if ($order["end_openid"] != $user["openid"]) {
			return $this->ajaxReturn($this->errorCode, "骑手未抢该订单");
		}
		\app\api\model\Order::where(["id" => $o_id])->update(["status" => 7]);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function CancelGrab()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$reason = $this->request->post("reason");
		$type = $this->request->post("type");
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		$from = $order["start_openid"] == $user["openid"] ? "user" : "rider";
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		if ($type == 1) {
			if ($order["status"] != 3) {
				return $this->ajaxReturn($this->errorCode, "订单不是待取货状态");
			}
			if ($order["end_openid"] != $user["openid"] && $from == "rider") {
				return $this->ajaxReturn($this->errorCode, "骑手未抢该订单");
			}
			\app\api\model\Order::where(["id" => $o_id])->update(["status" => 9, "cancel_time" => time(), "cancel_from" => $from, "cancel_reason" => $reason]);
			$openid = $from == "user" ? $order["end_openid"] : $order["start_openid"];
			$set = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
			if ($set["mp_template_cancel"]) {
				MpMsgSubscribe::cancelOrder($o_id, $wxapp_id, $reason, $openid);
			}
			if ($set["template_cancel"]) {
				MsgSubscribe::cancelOrder($o_id, $wxapp_id, $reason, $openid);
			}
		} else {
			if ($order["status"] != 9) {
				return $this->ajaxReturn($this->errorCode, "订单状态有误");
			}
			\app\api\model\Order::where(["id" => $o_id])->update(["status" => 3, "is_refuse" => 1, "refuse_reason" => $reason]);
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function agreeCancel()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		if ($user["openid"] != $order["start_openid"] && $user["openid"] != $order["end_openid"]) {
			return $this->ajaxReturn($this->errorCode, "该订单不属于该用户");
		}
		if ($order["status"] != 9) {
			return $this->ajaxReturn($this->errorCode, "订单状态有误");
		}
		\think\facade\Db::startTrans();
		if ($order["type"] == 3) {
			$store = \think\facade\Db::name("zh_business")->find($order["store_id"]);
			if ($store["printtype"] == 2) {
				Printyilianyun::callPrints($o_id, $order["store_id"]);
			} else {
				Printer::cancel($o_id);
			}
		}
		if ($order["cancel_from"] == "user") {
			$data["status"] = 8;
			$plus = \think\facade\Db::name("dmh_school_order_plus_price")->where("o_id", $order["id"])->where("pay_status", 1)->where("is_refund", 0)->select()->toArray();
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
			if ($order["t_money"] > 0) {
				try {
					\app\api\service\PaymentService::instance($wxapp_id)->refund($order["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $order["t_money"], $order["t_money"], $order["pay_type"]);
					if ($order["co_id"]) {
						\app\api\model\UserCoupon::where(["id" => $order["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
					}
					\app\api\model\Order::where(["id" => $order["id"]])->update($data);
					\think\facade\Db::commit();
				} catch (\Exception $e) {
					return $this->ajaxReturn($this->errorCode, $e->getMessage());
				}
			} else {
				if ($order["co_id"]) {
					\app\api\model\UserCoupon::where(["id" => $order["co_id"]])->update(["use_status" => 0, "update_time" => time()]);
				}
				\app\api\model\Order::where(["id" => $order["id"]])->update($data);
				\think\facade\Db::commit();
			}
		} elseif ($order["cancel_from"] == "rider") {
			\app\api\model\Order::where(["id" => $o_id])->update(["status" => 2, "end_openid" => "", "cancel_time" => "", "s_money" => 0, "fxs_money" => 0, "store_money" => 0, "fx_store_money" => 0]);
		}
		\think\facade\Db::commit();
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function uploadProof()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$proof = $this->request->post("proof");
		$uid = $this->request->uid;
		$user = \app\api\model\WechatUser::where("u_id", $uid)->find();
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		if ($order["end_openid"] != $user["openid"]) {
			return $this->ajaxReturn($this->errorCode, "骑手未抢该订单");
		}
		if ($order["proof"]) {
			return $this->ajaxReturn($this->errorCode, "请勿重复操作");
		}
		if (!$proof) {
			return $this->ajaxReturn($this->errorCode, "请上传凭证");
		}
		if ($res = \app\api\model\Order::where(["id" => $o_id])->update(["proof" => $proof])) {
			return $this->ajaxReturn($this->successCode, "操作成功");
		} else {
			return $this->ajaxReturn($this->errorCode, "操作失败");
		}
	}
	public function confirmFinish()
	{
		$o_id = $this->request->post("o_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$uid = $this->request->uid;
		$order = \app\api\model\Order::where(["id" => $o_id, "wxapp_id" => $wxapp_id])->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "未找到订单");
		}
		$user = \app\api\model\WechatUser::where(["openid" => $order["end_openid"], "wxapp_id" => $wxapp_id])->find();
		if ($order["end_openid"] != $user["openid"]) {
			return $this->ajaxReturn($this->errorCode, "骑手未抢该订单");
		}
		\think\facade\Db::startTrans();
		try {
			$school = \think\facade\Db::name("school")->find($order["s_id"]);
			Distribution::index(2, $order["id"]);
			$res1 = $this->app->db->name("dmh_school_order")->where(["id" => $o_id])->update(["status" => "4"]);
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
				$store_money = \floatval($order["food_money"]) - \floatval($order["store_money"]) - \floatval($order["fx_store_money"]);
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
				return $this->ajaxReturn($this->successCode, "操作成功", 1);
			} else {
				throw new \Exception("操作失败");
			}
		} catch (\Exception $e) {
			return $this->ajaxReturn($this->errorCode, $e->getMessage());
		}
	}
}