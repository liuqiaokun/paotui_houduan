<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhmarketorder extends Common
{
	public function index()
	{
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["s_id"] = $this->request->post("s_id", "", "serach_in");
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$where["uid"] = $this->request->uid;
		$where["paystate"] = [">", 0];
		$field = "*";
		$orderby = "id desc";
		if ($this->request->post("type", "", "serach_in") == 1) {
			$res = \app\api\service\DmhmarketorderService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			$list = [];
			foreach ($res["list"] as $key => $v) {
				$list[$key] = $v;
				$wheress["m_id"] = $v["m_id"];
				$list[$key]["market"] = $this->app->db->name("dmh_market_goods")->where($wheress)->find();
				$stay = $this->app->db->name("dmh_goods_stay")->where("m_id", $v["m_id"])->where("u_id", $where["uid"])->find();
				if ($stay) {
					$list[$key]["stay"] = 1;
				} else {
					$list[$key]["stay"] = 0;
				}
			}
			$res["list"] = $list;
		} elseif ($this->request->post("type", "", "serach_in") == 2) {
			$data = ["s_id" => $where["s_id"], "wxapp_id" => $where["wxapp_id"], "purchase" => $where["uid"], "paystate" => [">", 0]];
			$res = \app\api\service\DmhmarketorderService::indexList(formatWhere($data), $field, $orderby, $limit, $page);
			$list = [];
			foreach ($res["list"] as $key => $v) {
				$list[$key] = $v;
				$wheress["m_id"] = $v["m_id"];
				$list[$key]["market"] = $this->app->db->name("dmh_market_goods")->where($wheress)->find();
			}
			$res["list"] = $list;
		} else {
			$res = \app\api\service\DmhmarketorderService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		}
		$list = [];
		foreach ($res["list"] as $key => $v) {
			$list[$key] = $v;
			$wheres = ["m_id" => $v["m_id"]];
			$list[$key]["con"] = $this->app->db->name("dmh_goods_stay")->where($wheres)->count();
			$goods = $this->app->db->name("dmh_market_goods")->where($wheres)->find();
			$list[$key]["user"] = $this->app->db->name("wechat_user")->where("u_id", $goods["u_id"])->find();
		}
		$res["list"] = $list;
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function details()
	{
		$data["id"] = input("id", "", "trim");
		$field = "*";
		$res = checkData(\app\api\model\Dmhmarketorder::field($field)->where($data)->find());
		$res["market"] = $this->app->db->name("dmh_market_goods")->where("m_id", $res["m_id"])->find();
		$res["create_time"] = date("Y-m-d", $res["create_time"]);
		$res["pay_time"] = date("Y-m-d H:i:s", $res["pay_time"]);
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function lit()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["s_id"] = $this->request->get("s_id", "", "serach_in");
		$where["wxapp_id"] = $this->request->get("wxapp_id", "", "serach_in");
		$where["uid"] = $this->request->uid;
		$field = "state,phone,name,other";
		$orderby = "id desc";
		$res = \app\api\service\DmhmarketorderService::litList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$postField = "state,create_time,phone,name,other";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$res = \app\api\service\DmhmarketorderService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function pay()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$s_id = $this->request->post("s_id");
		$uids = $this->request->post("u_id");
		$phone = $this->request->post("phone");
		$name = $this->request->post("linkman");
		$other = $this->request->post("remark");
		$m_id = $this->request->post("id");
		$peiz = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$pay = \think\facade\Db::name("dmh_market_goods")->where("m_id", $m_id)->find();
		$config = ["app_id" => $peiz["appid"], "mch_id" => $peiz["mch_id"], "key" => $peiz["mch_key"], "notify_url" => "https://" . $_SERVER["HTTP_HOST"] . "/api/Dmhmarketorder/notify"];
		$goods = $this->app->db->name("dmh_market_goods")->where("m_id", $m_id)->find();
		if ($goods["stock"] <= 0) {
			return $this->ajaxReturn($this->errorCode, "订单库存已售罄");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$app = \EasyWeChat\Factory::payment($config);
		$openid = $user["openid"];
		$order_id = "DMHES" . time() . rand(10, 99) . rand(10, 99);
		$pay_info = ["trade_type" => "JSAPI", "body" => $goods["title"], "out_trade_no" => $order_id, "total_fee" => $pay["pay"] * 100, "openid" => $openid];
		$insert["wxapp_id"] = $wxapp_id;
		$insert["m_id"] = $m_id;
		$insert["s_id"] = $s_id;
		$insert["uid"] = $uids;
		$insert["purchase"] = $uid;
		$insert["phone"] = $phone;
		$insert["name"] = $name;
		$insert["other"] = $other;
		$insert["oid"] = $order_id;
		$insert["pay"] = $pay["pay"];
		$insert["create_time"] = time();
		$insert["state"] = 0;
		$insert["pay_type"] = $peiz["pay_type"];
		$wheres["wxapp_id"] = $wxapp_id;
		$wheres["s_id"] = $s_id;
		$school = $this->app->db->name("school")->where($wheres)->find();
		$second_rate = sprintf("%.2f", $pay["pay"] * ($school["second_rate"] / 100));
		$fx_second_rate = sprintf("%.2f", $pay["pay"] * ($school["fx_second_rate"] / 100));
		$insert["commission"] = $second_rate;
		$insert["branch"] = $fx_second_rate;
		$res = \think\facade\Db::name("dmh_market_order")->insert($insert);
		if ($res) {
			$goods = \think\facade\Db::name("dmh_market_goods")->where("m_id", $m_id)->find();
			if ($goods["stock"] >= 1) {
				\think\facade\Db::name("dmh_market_goods")->where("m_id", $m_id)->dec("stock", 1)->update();
			} else {
				\think\facade\Db::name("dmh_market_goods")->where("m_id", $m_id)->update(["state" => 1]);
			}
		}
		try {
			$notify_url = "http://" . $this->request->host() . "/api/Dmhmarketorder/payResult/wxapp_id/" . $wxapp_id;
			$js_pay = \app\api\service\PaymentService::instance($wxapp_id)->create($openid, $order_id, $pay["pay"], "下单", $peiz["pay_type"], ["notify_url" => $notify_url]);
			$js_pay["order_id"] = $order_id;
			return $this->ajaxReturn($this->successCode, "下单成功", $js_pay);
		} catch (\Exception $e) {
			return $this->ajaxReturn($this->errorCode, $e->getMessage());
		}
	}
	public function notify()
	{
		$postStr = file_get_contents("php://input");
		$getData = $this->xmlstr_to_array($postStr);
		$order = \think\facade\Db::name("dmh_market_order")->where("oid", $getData["out_trade_no"])->find();
		if ($order && $order["status"] == 0 && $getData["return_code"] === "SUCCESS") {
			if ($getData["result_code"] === "SUCCESS" && $getData["total_fee"] == $order["pay"] * 100) {
				\think\facade\Db::name("dmh_market_order")->where("oid", $getData["out_trade_no"])->update(["state" => 1, "pay_time" => time(), "paystate" => 1]);
			}
		}
		$str = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
		echo $str;
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
				$order = \think\facade\Db::name("dmh_market_order")->where("oid", $getData["outTradeNo"])->find();
				trace("进入回调7887" . $order["wxapp_id"] . "==" . $getData["outTradeNo"], "notify_" . date("Y_m_d"));
				if ($order["paystate"] == 0) {
					\think\facade\Db::name("dmh_market_order")->where("oid", $getData["outTradeNo"])->update(["state" => 1, "pay_time" => time(), "paystate" => 1]);
				}
			}
		} else {
			$app = \app\api\service\PaymentService::instance($wxapp_id)::getPayApp();
			$response = $app->handlePaidNotify(function ($message, $fail) {
				trace($message, "notify_" . date("Y_m_d"));
				$order = \think\facade\Db::name("dmh_market_order")->where("oid", $message["out_trade_no"])->find();
				trace("进入回调7887" . $order["wxapp_id"] . "==" . $message["out_trade_no"], "notify_" . date("Y_m_d"));
				if (!$order || $order->status == 2) {
					return true;
				}
				if ($message["return_code"] === "SUCCESS") {
					if ($message["result_code"] === "SUCCESS" && $order["paystate"] == 0) {
						\think\facade\Db::name("dmh_market_order")->where("oid", $message["out_trade_no"])->update(["state" => 1, "pay_time" => time(), "paystate" => 1]);
					}
				} else {
					return $fail("通信失败，请稍后再通知我");
				}
				return true;
			});
			return $response->send();
		}
	}
	public function xmlstr_to_array($xmlstr)
	{
		libxml_disable_entity_loader(true);
		$xmlstring = simplexml_load_string($xmlstr, "SimpleXMLElement", LIBXML_NOCDATA);
		$val = json_decode(json_encode($xmlstring), true);
		return $val;
	}
	public function secondRefund()
	{
		$where["wxapp_id"] = input("wxapp_id", "", "trim");
		$where["oid"] = input("oid", "", "trim");
		$where["s_id"] = input("s_id", "", "trim");
		$reason = input("reason", "", "trim");
		if (empty($reason)) {
			return $this->ajaxReturn($this->errorCode, "申请理由不能为空");
		}
		$where["purchase"] = $this->request->uid;
		$order = $this->app->db->name("dmh_market_order")->where($where)->update(["paystate" => 3, "reason" => $reason]);
		if ($order) {
			return $this->ajaxReturn($this->successCode, "申请成功");
		} else {
			return $this->ajaxReturn($this->errorCode, "申请失败");
		}
	}
	public function secondRefundOper()
	{
		$where["wxapp_id"] = input("wxapp_id", "", "trim");
		$where["oid"] = input("oid", "", "trim");
		$where["s_id"] = input("s_id", "", "trim");
		$type = input("type", "", "trim");
		$where["uid"] = $this->request->uid;
		$order = $this->app->db->name("dmh_market_order")->where($where)->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "订单查找失败");
		}
		if ($type == 1) {
			$goods = $this->app->db->name("dmh_market_goods")->where("m_id", $order["m_id"])->find();
			$stock = $goods["stock"] + 1;
			$res = $this->app->db->name("dmh_market_order")->where($where)->update(["paystate" => 4]);
			$this->app->db->name("dmh_market_goods")->where("m_id", $order["m_id"])->update(["stock" => $stock]);
			\app\api\service\PaymentService::instance($where["wxapp_id"])->refund($where["oid"], "T" . date("YmdHis") . rand(1000, 9999), $order["pay"], $order["pay"], $order["pay_type"]);
		} else {
			$res = $this->app->db->name("dmh_market_order")->where($where)->update(["paystate" => 1]);
		}
		if ($res) {
			return $this->ajaxReturn($this->successCode, "操作成功");
		} else {
			return $this->ajaxReturn($this->errorCode, "操作失败");
		}
	}
	public function confirm()
	{
		$where["wxapp_id"] = input("wxapp_id", "", "trim");
		$where["oid"] = input("oid", "", "trim");
		$where["s_id"] = input("s_id", "", "trim");
		$where["purchase"] = $this->request->uid;
		$order = $this->app->db->name("dmh_market_order")->where($where)->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "查找不到该订单");
		}
		Distribution::index(3, $order["id"]);
		$pay = $order["pay"] - ($order["commission"] + $order["branch"]);
		$user = $this->app->db->name("wechat_user")->where("u_id", $order["uid"])->find();
		$balance = $user["balance"] + $pay;
		$userpay = $this->app->db->name("wechat_user")->where("u_id", $order["uid"])->update(["balance" => $balance]);
		$res = $this->app->db->name("dmh_market_order")->where($where)->update(["paystate" => 2, "commission" => $order["commission"]]);
		$logarray = ["uid" => $order["uid"], "type" => 2, "price" => $pay, "operate" => 0, "wxapp_id" => $order["wxapp_id"], "remark" => "二手市场收入" . $order["id"]];
		$log = $this->app->db->name("user_account_log")->insert($logarray);
		if ($res && $userpay) {
			return $this->ajaxReturn($this->successCode, "操作成功");
		} else {
			return $this->ajaxReturn($this->errorCode, "您已确认收货请勿重复操作");
		}
	}
	public function cancels()
	{
		if (!input("ids", "", "trim")) {
			return $this->ajaxReturn($this->errorCode, "商品id不能为空");
		}
		$goods = $this->app->db->name("dmh_market_goods")->where("m_id", input("ids", "", "trim"))->find();
		if (!$goods) {
			return $this->ajaxReturn($this->errorCode, "商品信息不存在");
		}
		$stock = $goods["stock"] + 1;
		$res = $this->app->db->name("dmh_market_goods")->where("m_id", $goods["m_id"])->update(["stock" => $stock]);
		if ($res) {
			return $this->ajaxReturn($this->successCode, "操作成功");
		} else {
			return $this->ajaxReturn($this->errorCode, "操作失败");
		}
	}
}