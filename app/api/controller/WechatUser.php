<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class WechatUser extends Common
{
	public function update()
	{
		$postField = "u_id,t_sex,auth_sid,t_name,phone,run_status";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$data["auth_time"] = time();
		if (empty($data["u_id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["u_id"] = $data["u_id"];
		$res = \app\api\service\WechatUserService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function edit()
	{
		$postField = "u_id,nickname,avatar,alipay_name,alipay_account";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		if (empty($data["u_id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["u_id"] = $data["u_id"];
		$res = \think\facade\Db::name("wechat_user")->where($where)->update($data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function view()
	{
		$data["u_id"] = $this->request->uid;
		$res = checkData(\app\api\model\WechatUser::find($data["u_id"]));
		$s_id = $this->request->post("s_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$coupon_count = \think\facade\Db::name("user_coupon")->where("u_id", $data["u_id"])->where("s_id", $s_id)->where("use_status", 0)->count();
		if (!$res["code"]) {
			$code = $this->code($wxapp_id, $data["u_id"]);
			\think\facade\Db::name("wechat_user")->where("u_id", $data["u_id"])->update(["code" => $code]);
			$res["code"] = $code;
		}
		$res["coupon"] = $coupon_count;
		if ($res["deadtime"] < time()) {
			\think\facade\Db::name("wechat_user")->where("u_id", $data["u_id"])->update(["deadtime" => null]);
		}
		$res["is_vip"] = $res["deadtime"] > time() ? 1 : 0;
		$res["deadtime"] = date("Y-m-d H:i:s", $res["deadtime"]);
		$yesterday_start = date("Y-m-d", strtotime("-1day")) . " 00:00:00";
		$yesterday_end = date("Y-m-d", strtotime("-1day")) . " 23:59:59";
		$store = \think\facade\Db::name("zh_business")->where("wxapp_id", $wxapp_id)->where("wxadmin_name", $data["u_id"])->find();
		$res["yesterday"] = \think\facade\Db::name("user_account_log")->where("wxapp_id", $wxapp_id)->where("uid", $res["u_id"])->where("operate", 0)->where("remark", "like", "%用户跑腿收入%")->where("addtime", "between", [$yesterday_start, $yesterday_end])->sum("price");
		$res["follow_num"] = \think\facade\Db::name("user_follow")->where("status", 1)->where("uid", $data["u_id"])->where("wxapp_id", $wxapp_id)->count();
		$res["fans_num"] = \think\facade\Db::name("user_follow")->where("status", 1)->where("f_uid", $data["u_id"])->where("wxapp_id", $wxapp_id)->count();
		$res["articles_num"] = \think\facade\Db::name("zh_articles")->where("s_id", $s_id)->where("u_id", $data["u_id"])->where("wxapp_id", $wxapp_id)->where("delete_time", "exp", "is null")->count();
		$res["plat_has_store"] = $store ? 1 : 0;
		$res["has_store"] = $store["s_id"] == $s_id ? 1 : 0;
		$settled = \think\facade\Db::name("dmh_settled")->where(["u_id" => $data["u_id"], "wxapp_id" => $wxapp_id])->find();
		$res["store_check_status"] = $settled ? $settled["state"] : -1;
		$res["is_distribution"] = \think\facade\Db::name("school")->where("s_id", $s_id)->value("is_distribution");
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function code($wxapp_id, $uid)
	{
		$set = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$path = "gc_school/pages/home/index";
		$config = ["app_id" => $set["appid"], "secret" => $set["appsecret"], "response_type" => "array"];
		$app = \EasyWeChat\Factory::miniProgram($config);
		$response = $app->app_code->getUnlimit("spid=" . $uid, ["page" => $path, "width" => 50]);
		if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
			$qr_filename = "https://" . $_SERVER["HTTP_HOST"] . "/wxmp/" . $response->saveAs("wxmp/", "mp" . $uid . ".png");
		}
		return $qr_filename;
	}
	public function rewardLst()
	{
		$uid = $this->request->uid;
		$wxapp_id = $this->request->post("wxapp_id");
		$page = $this->request->post("page");
		$data = \think\facade\Db::name("article_pay")->alias("p")->join("zh_articles a", "a.article_id = p.a_id")->join("wechat_user u", "a.u_id = u.u_id")->where("p.wxapp_id", $wxapp_id)->where("p.uid", $uid)->where("p.type", 2)->where("p.status", 1)->field("p.*,a.content,u.avatar,u.nickname,a.is_anonymous")->page($page, 10)->select();
		return $this->ajaxReturn($this->successCode, "返回成功", $data);
	}
	public function spreadinfo()
	{
		$data["u_id"] = $this->request->uid;
		$res = checkData(\app\api\model\WechatUser::find($data["u_id"]));
		$where["wxapp_id"] = $res["wxapp_id"];
		$child = \think\facade\Db::name("wechat_user")->field("u_id,spid")->where("spid", $res["u_id"])->where($where)->select();
		$res["spread_num"] = count($child);
		$res["spread_money"] = \think\facade\Db::name("spread_account_log")->where($where)->where("u_id", $res["u_id"])->where("operate", 1)->where("type", ">", 0)->sum("price");
		$count = 0;
		foreach ($child as &$v) {
			$count += \think\facade\Db::name("wechat_user")->where("spid", $v["u_id"])->where($where)->count();
		}
		$res["team_count"] = $count + $res["spread_num"];
		$withwhere["u_id"] = $res["u_id"];
		$withwhere["is_spread"] = 1;
		$withwhere["wxapp_id"] = $res["wxapp_id"];
		$res["tixian_have"] = \think\facade\Db::name("user_withdraw")->where($withwhere)->where("status", 2)->sum("price");
		$res["tixian_ing"] = \think\facade\Db::name("user_withdraw")->where($withwhere)->where("status", 1)->sum("price");
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function updatePid()
	{
		$data["u_id"] = $this->request->uid;
		$spid = $this->request->post("spid");
		$par = \think\facade\Db::name("wechat_user")->find($spid);
		if ($par["spid"] != $data["u_id"]) {
			$res = \think\facade\Db::name("wechat_user")->where("u_id", $data["u_id"])->update(["spid" => $spid, "spid_time" => date("Y-m-d H:i:s")]);
		}
		return $this->ajaxReturn($this->successCode, "操作成功", 0);
	}
	public function openuser()
	{
		$unionId = input("unionId");
		$openopenid = input("openId");
		$res = $this->app->db->name("wechat_user")->where("unionid", $unionId)->find();
		if ($res) {
			$this->app->db->name("wechat_user")->where("unionid", $unionId)->update(["openopenid" => $openopenid]);
			$token = $this->setToken($res["u_id"]);
		} else {
			$data = ["nickname" => input("nickName"), "avatar" => input("avatarUrl"), "openopenid" => $openopenid, "unionid" => $unionId, "deadtime" => time()];
			$user = $this->app->db->name("wechat_user")->insertGetId($data);
			$token = $this->setToken($user);
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $token);
	}
	public function users()
	{
		$data["u_id"] = $this->request->uid;
		$res = \think\facade\Db::name("wechat_user")->where("u_id", $data["u_id"])->find();
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function userss()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$u_id = $this->request->post("u_id");
		$res = \think\facade\Db::name("wechat_user")->where(["wxapp_id" => $wxapp_id, "u_id" => $u_id])->find();
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function addSubscribe()
	{
		$uid = $this->request->uid;
		$temp_id = $this->request->post("temp_id");
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$temp_id) {
			return $this->ajaxReturn($this->errorCode, "订阅消息不可为空", 0);
		}
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "用户不存在", 0);
		}
		$where["u_id"] = $uid;
		$where["openid"] = $user["openid"];
		$where["temp_id"] = $temp_id;
		$where["wxapp_id"] = $wxapp_id;
		$data = \think\facade\Db::name("subscribe_count")->where($where)->find();
		if (empty($data)) {
			$res = \think\facade\Db::name("subscribe_count")->insert(["openid" => $user["openid"], "temp_id" => $temp_id, "wxapp_id" => $wxapp_id, "u_id" => $uid]);
		} else {
			$res = \think\facade\Db::name("subscribe_count")->where("id", $data["id"])->inc("count", 1)->update();
		}
		if ($res) {
			return $this->ajaxReturn($this->successCode, "操作成功", $res);
		} else {
			return $this->ajaxReturn($this->errorCode, "操作失败", $res);
		}
	}
	public function subscribeList()
	{
		$uid = $this->request->uid;
		$wxapp_id = $this->request->post("wxapp_id");
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$template_list = \think\facade\Db::name("subscribe_count")->where(["openid" => $user["openid"], "wxapp_id" => $wxapp_id])->select();
		return $this->ajaxReturn($this->successCode, "操作成功", $template_list);
	}
	public function subscribeListss()
	{
		$uid = $this->request->uid;
		$wxapp_id = $this->request->post("wxapp_id");
		$user = \think\facade\Db::name("wechat_user")->find($uid);
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$template_list = [["name" => "新订单提醒消息", "temp_id" => $setting["template_new"]], ["name" => "骑手抢单提醒", "temp_id" => $setting["template_grab"]], ["name" => "申请取消订单通知", "temp_id" => $setting["template_cancel"]], ["name" => "商家订阅消息", "temp_id" => $setting["template_store"]], ["name" => "圈子留言提醒", "temp_id" => $setting["template_comment"]], ["name" => "二手市场支付提醒", "temp_id" => $setting["template_pay"]], ["name" => "聊天消息提醒", "temp_id" => $setting["template_notice"]], ["name" => "订单取消通知(骑手)", "temp_id" => $setting["template_cancel_rider"]], ["name" => "商家拒单提醒", "temp_id" => $setting["template_refuse"]]];
		foreach ($template_list as &$v) {
			$where["openid"] = $user["openid"];
			$where["temp_id"] = $v["temp_id"];
			$count = \think\facade\Db::name("subscribe_count")->where($where)->value("count");
			$v["number"] = $count ? $count : 0;
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $template_list);
	}
}