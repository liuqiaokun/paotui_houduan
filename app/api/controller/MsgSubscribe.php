<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class MsgSubscribe extends Common
{
	public function robot_send($order)
	{
		$school = \think\facade\Db::name("school")->find($order["s_id"]);
		if ($school["robot_key"]) {
			$thing10 = mb_substr($order["sh_addres"], 0, 26);
			$type_arr = [1 => "取快递", 2 => "寄快递", 3 => "超市食堂", 4 => "万能任务"];
			$data["linkadr"] = $_SERVER["HTTP_HOST"];
			$data["sendkey"] = $school["robot_key"];
			$data["remark"] = base64_encode("有新订单啦，请相关骑手注意。\n >类型：<font color='comment'>" . $type_arr[$order["type"]] . "</font>\n>跑腿费：<font color='comment'>" . $order["total"] . "元</font>\n>送货地点：<font color='comment'>" . $thing10 . "</font> ");
			$url = "http://send.fkynet.net/api/LinkExt/check";
			$res = \app\api\service\sendMsgService::curlPost($url, $data);
		}
	}
	public function toAllRider($ordersn, $wxapp_id)
	{
		$ordersn = $ordersn ? $ordersn : $this->request->post("ordersn");
		$wxapp_id = $wxapp_id ? $wxapp_id : $this->request->post("wxapp_id");
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if ($config["mp_template_new"]) {
			MpMsgSubscribe::toAllRider($wxapp_id, $ordersn);
		}
		if ($config["template_new"]) {
			if (!$ordersn) {
				return $this->ajaxReturn($this->errorCode, "订单号不能为空");
			}
			$order = \think\facade\Db::name("dmh_school_order")->where("ordersn", $ordersn)->find();
			self::robot_send($order);
			$user = \think\facade\Db::name("wechat_user")->where("run_status", 2)->where("auth_sid", $order["s_id"])->where("wxapp_id", $wxapp_id)->select();
			$data = [];
			$data["template_id"] = $config["template_new"];
			$data["page"] = "gc_school/pages/grab/index";
			$school = \think\facade\Db::name("school")->find($order["sh_school"]);
			$temp = $school["s_name"] . $order["sh_addres"];
			$thing10 = $order["sh_addres"] ? mb_strlen($temp) > 20 ? mb_substr($temp, 0, 17) . "..." : $temp : "看详情";
			$data["data"] = ["character_string4" => ["value" => $order["ordersn"]], "amount3" => ["value" => $order["total"]], "thing10" => ["value" => $thing10], "date6" => ["value" => date("Y-m-d H:i:s", time())]];
			foreach ($user as &$v) {
				$data["touser"] = $v["openid"];
				\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
			}
		}
	}
	public static function chat($wxapp_id, $user, $sender)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$data = [];
		$data["touser"] = $user["openid"];
		$data["template_id"] = $config["template_notice"];
		$data["page"] = "gc_school/pages/chat/chat?s_id=" . $sender["u_id"];
		$data["data"] = ["thing2" => ["value" => "收到新的聊天信息，请查看"], "thing1" => ["value" => $sender["nickname"]], "time3" => ["value" => date("Y-m-d H:i:s", time())]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public function toPublisher($o_id, $wxapp_id)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$data = [];
		$data["touser"] = $order["start_openid"];
		$data["template_id"] = $config["template_grab"];
		$data["page"] = "gc_school/pages/home/index";
		$data["data"] = ["number4" => ["value" => $order["ordersn"]], "amount9" => ["value" => $order["total"]], "thing10" => ["value" => "您的订单已被接单"], "time6" => ["value" => date("Y-m-d H:i:s", $order["createtime"])]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public function toStore($o_id, $wxapp_id)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$store = \think\facade\Db::name("zh_business")->find($order["store_id"]);
		$user = \think\facade\Db::name("wechat_user")->find($store["wxadmin_name"]);
		$data = [];
		$data["touser"] = $user["openid"];
		$data["template_id"] = $config["template_store"];
		$data["page"] = "gc_school/pages/shop/order";
		$data["data"] = ["character_string6" => ["value" => $order["ordersn"]], "amount10" => ["value" => $order["guess_prcie"]], "date4" => ["value" => date("Y-m-d H:i:s", $order["createtime"])]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public function cancelOrder($o_id, $wxapp_id, $reason, $openid, $type)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$data = [];
		$data["touser"] = $openid;
		$data["template_id"] = $config["template_cancel"];
		$page = $type == 1 ? "gc_school/pages/order/detail?id=" . $o_id : "gc_school/pages/shop/order";
		$data["page"] = $page;
		$data["data"] = ["character_string6" => ["value" => $order["ordersn"]], "time7" => ["value" => date("Y-m-d H:i:s", time())], "thing4" => ["value" => $reason]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public function cancelOrderRider($o_id, $wxapp_id, $openid)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$data = [];
		$data["touser"] = $openid;
		$data["template_id"] = $config["template_cancel_rider"];
		$data["page"] = "gc_school/pages/order/detail?id=" . $o_id;
		$data["data"] = ["character_string20" => ["value" => $order["ordersn"]], "thing1" => ["value" => $order["cancel_reason"]], "date2" => ["value" => date("Y-m-d H:i:s", time())]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public function refuseOrder($o_id, $wxapp_id, $openid)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$data = [];
		$data["touser"] = $openid;
		$data["template_id"] = $config["template_refuse"];
		$data["page"] = "gc_school/pages/order/detail?id=" . $o_id;
		$data["data"] = ["character_string1" => ["value" => $order["ordersn"]], "thing5" => ["value" => $order["refuse_reason"]], "date2" => ["value" => date("Y-m-d H:i:s", $order["createtime"])]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public function toCommentator($content, $uid, $wxapp_id, $article_id)
	{
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$data = [];
		$data["touser"] = $user["openid"];
		$data["template_id"] = $config["template_comment"];
		$data["page"] = "gc_school/pages/article/detail?id=" . $article_id;
		$data["data"] = ["thing3" => ["value" => $content], "date2" => ["value" => date("Y-m-d H:i:s")]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public function toSeller()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$o_id = $this->request->post("ordersn");
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$o_id) {
			return $this->ajaxReturn($this->errorCode, "订单号不能为空");
		}
		$order = \think\facade\Db::name("dmh_market_order")->where("oid", $o_id)->find();
		if ($config["mp_template_pay"]) {
			MpMsgSubscribe::toSeller($wxapp_id, $order);
		}
		if ($config["template_pay"]) {
			$user = \think\facade\Db::name("wechat_user")->find($order["uid"]);
			$good = \think\facade\Db::name("dmh_market_goods")->find($order["m_id"]);
			$data = [];
			$data["template_id"] = $config["template_pay"];
			$data["page"] = "gc_school/pages/secondhand/orderdetail?id=" . $order["id"];
			$data["touser"] = $user["openid"];
			$data["data"] = ["thing1" => ["value" => $good["title"]], "amount2" => ["value" => $order["pay"] . "元"], "thing14" => ["value" => $order["name"] . "-" . $order["phone"]], "date10" => ["value" => date("Y-m-d H:i:s", $order["create_time"])]];
			\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
		}
	}
}