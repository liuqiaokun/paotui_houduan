<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class MpMsgSubscribe extends Common
{
	public function send($setting, $data, $openid, $temp_id, $path = "/gc_school/pages/home/index")
	{
		$config = ["app_id" => $setting["mp_appid"], "secret" => $setting["mp_secret"]];
		$app = \EasyWeChat\Factory::officialAccount($config);
		$result = $app->template_message->send(["touser" => $openid, "template_id" => $temp_id, "miniprogram" => ["appid" => $setting["appid"], "pagepath" => $path], "data" => $data]);
	}
	public static function toAllRider($wxapp_id, $ordersn)
	{
		if (!$ordersn) {
			return self::ajaxReturn(self::errorCode, "订单号不能为空");
		}
		$order = \think\facade\Db::name("dmh_school_order")->where("ordersn", $ordersn)->find();
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$user = \think\facade\Db::name("wechat_user")->where("run_status", 2)->where("auth_sid", $order["s_id"])->where("wxapp_id", $wxapp_id)->select();
		$data = ["first" => "有新订单了，快来接单吧", "keyword1" => $order["ordersn"], "keyword2" => date("Y-m-d H:i:s"), "remark" => "点击进入小程序查看"];
		foreach ($user as &$v) {
			if ($v["mp_openid"]) {
				self::send($config, $data, $v["mp_openid"], $config["mp_template_new"], "gc_school/pages/grab/index");
			}
		}
	}
	public static function toPublisher($o_id, $wxapp_id)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$user = \think\facade\Db::name("wechat_user")->where("openid", $order["start_openid"])->where("wxapp_id", $order["wxapp_id"])->find();
		$type_list = ["", "取件", "寄件", "食堂超市", "万能任务"];
		$data = ["first" => "您好，您发布的任务已经有骑手接单了。", "keyword1" => $order["ordersn"], "keyword2" => $order["total"], "keyword3" => $type_list[$order["type"]], "remark" => "点击进入小程序查看"];
		if ($user["mp_openid"]) {
			self::send($config, $data, $user["mp_openid"], $config["mp_template_grab"]);
		}
	}
	public static function toStore($o_id, $wxapp_id)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$store = \think\facade\Db::name("zh_business")->find($order["store_id"]);
		$user = \think\facade\Db::name("wechat_user")->find($store["wxadmin_name"]);
		$data = ["first" => "有用户在您店铺下单啦！", "keyword1" => $order["ordersn"], "keyword2" => $order["food_money"], "remark" => "点击进入小程序查看"];
		if ($user["mp_openid"]) {
			self::send($config, $data, $user["mp_openid"], $config["mp_template_store"], "gc_school/pages/shop/order");
		}
	}
	public static function cancelOrder($o_id, $wxapp_id, $reason, $openid, $type)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$user = \think\facade\Db::name("wechat_user")->where("openid", $openid)->where("wxapp_id", $wxapp_id)->find();
		$remark = $type == 1 ? "您好，对方发起了取消订单申请，理由为" . $reason : "商家同意取消，该订单已取消";
		$data = ["first" => $remark, "keyword1" => $order["ordersn"], "keyword2" => date("Y-m-d H:i:s"), "remark" => "点击进入小程序查看"];
		if ($user["mp_openid"]) {
			self::send($config, $data, $user["mp_openid"], $config["mp_template_cancel"]);
		}
	}
	public function refuseOrder($o_id, $wxapp_id, $openid)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$order = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$user = \think\facade\Db::name("wechat_user")->where("openid", $openid)->where("wxapp_id", $wxapp_id)->find();
		$data = ["first" => "商家拒绝了该订单的申请", "keyword1" => $order["ordersn"], "keyword2" => $order["refuse_reason"], "keyword3" => date("Y-m-d H:i:s"), "remark" => "点击进入小程序查看"];
		if ($user["mp_openid"]) {
			self::send($config, $data, $user["mp_openid"], $config["template_refuse_mp"]);
		}
	}
	public function toCommentator($content, $uid, $wxapp_id, $article_id)
	{
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$data = [];
		$data["touser"] = $user["mp_openid"];
		$data["template_id"] = $config["template_grab"];
		$data["page"] = "gc_school/pages/article/detail?id=" . $article_id;
		$data["data"] = ["thing3" => ["value" => $content], "date2" => ["value" => date("Y-m-d H:i:s")]];
		\app\api\service\sendMsgService::sendTemplateMsg($config, $data);
	}
	public static function toSeller($wxapp_id, $order)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$user = \think\facade\Db::name("wechat_user")->find($order["uid"]);
		$data = ["first" => "您好，有用户购买了您发布的商品", "keyword1" => $order["oid"], "keyword2" => $order["pay"], "keyword3" => date("Y-m-d H:i:s", $order["create_time"]), "remark" => "点击进入小程序查看"];
		if ($user["mp_openid"]) {
			self::send($config, $data, $user["mp_openid"], $config["mp_template_pay"]);
		}
	}
}