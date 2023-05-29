<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhBusiness extends Common
{
	public function update()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		$postField = "business_id,timeslot,start_time,end_time,business_name,business_address,phone,business_image,status,box_fee,service_price,method,is_self_lifting_open";
		$data = $this->request->only(explode(",", $postField), "get", null);
		if (empty($data["business_id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["business_id"] = $data["business_id"];
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$where["wxadmin_name"] = $uid;
		$res = \app\api\service\ZhBusinessService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function view()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		$data = ["wxadmin_name" => $uid, "wxapp_id" => $wxapp_id];
		$field = "*";
		$res = checkData(\app\api\model\ZhBusiness::field($field)->where($data)->find());
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function businessOrderList()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "wxadmin_name" => $uid])->value("business_id");
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["status"] = $this->request->get("status");
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$where["store_id"] = $businessId;
		$field = "*";
		$orderby = "id desc";
		$res = \think\facade\Db::name("dmh_school_order")->where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		foreach ($res["data"] as &$v) {
			$v["good_details"] = explode(";", $v["good_details"]);
			$v["start_time"] = date("Y-m-d H:i:s", $v["start_time"]);
			$v["food_money"] = $v["food_money"] ?: 0;
			$rider = \think\facade\Db::name("wechat_user")->where("openid", $v["end_openid"])->find();
			$v["rider_phone"] = $rider["phone"];
			$v["rider_name"] = $rider["t_name"];
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function index()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		$businessData = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "wxadmin_name" => $uid])->find();
		$school = \think\facade\Db::name("school")->find($businessData["s_id"]);
		$is_inform = $businessData["balance"] < $school["threshold"] ? 1 : 0;
		$schoolName = \think\facade\Db::name("school")->where(["wxapp_id" => $wxapp_id, "s_id" => $businessData["s_id"]])->value("s_name");
		$where = ["wxapp_id" => $wxapp_id, "s_id" => $businessData["s_id"], "store_id" => $businessData["business_id"], "status" => 4];
		$foodMoney = \think\facade\Db::name("dmh_school_order")->where($where)->sum("food_money");
		$storeMoney = \think\facade\Db::name("dmh_school_order")->where($where)->sum("store_money");
		$fxstoreMoney = \think\facade\Db::name("dmh_school_order")->where($where)->sum("fx_store_money");
		$orderNum = \think\facade\Db::name("dmh_school_order")->where($where)->count("id");
		$orderDayNum = \think\facade\Db::name("dmh_school_order")->where($where)->whereDay("createtime", date("Y-m-d", time()))->count("id");
		$res = ["expire_time" => date("Y-m-d H:i:s", $businessData["expire_time"]), "school_name" => $schoolName, "sumMoney" => round(\floatval($foodMoney) - \floatval($storeMoney) - \floatval($fxstoreMoney), 2), "order_num" => $orderNum, "order_day_num" => $orderDayNum, "is_inform" => $is_inform, "info" => $businessData, "store_balance" => $user["store_balance"], "store_bean_switch" => $school["store_bean_switch"]];
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function code($wxapp_id, $data)
	{
		$set = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$path = "gc_school/pages/foold/foold";
		$config = ["app_id" => $set["appid"], "secret" => $set["appsecret"], "response_type" => "array"];
		$app = \EasyWeChat\Factory::miniProgram($config);
		$response = $app->app_code->getUnlimit("id=" . $data["business_id"] . "&s_id=" . $data["s_id"], ["page" => $path, "width" => 50]);
		if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
			$qr_filename = "https://" . $_SERVER["HTTP_HOST"] . "/store/" . $response->saveAs("store/", "mp" . $data["business_id"] . ".png");
		}
		return $qr_filename;
	}
	public function businessPointPay()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		$vipMoney = $this->request->post("money");
		if (!$vipMoney) {
			return $this->ajaxReturn($this->errorCode, "缺少金额参数");
		}
		$vipType = $this->request->post("type");
		$number = $this->request->post("number");
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "wxadmin_name" => $uid])->value("business_id");
		$orderSn = "P" . date("YmdHis") . rand(1000, 9999);
		$rate = \floatval($vipMoney) * \floatval($sSetting["bean_rate"]) / 100;
		$data = ["order_sn" => $orderSn, "money" => $vipMoney, "number" => $number, "commission" => $rate, "status" => 0, "createtime" => time(), "vip_type" => 1, "wxapp_id" => $wxapp_id, "s_id" => $s_id, "u_id" => $uid, "business_id" => $businessId, "pay_type" => $wxSetting["pay_type"]];
		$r = \think\facade\Db::name("zh_vip_order")->insert($data);
		if ($r) {
			try {
				$notify_url = "http://" . $this->request->host() . "/api/ZhBusiness/pointPayResult/wxapp_id/" . $wxapp_id;
				$js_pay = \app\api\service\PaymentService::instance($wxapp_id)->create($user["openid"], $orderSn, $vipMoney, "会员充值", $wxSetting["pay_type"], ["notify_url" => $notify_url]);
				return $this->ajaxReturn($this->successCode, "操作成功", ["paydata" => $js_pay]);
			} catch (\Exception $e) {
				return $this->ajaxReturn($this->errorCode, $e->getMessage());
			}
		}
	}
	public function pointPayResult()
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
				$order = \think\facade\Db::name("zh_vip_order")->where(["order_sn" => $message["out_trade_no"]])->find();
				if (!$order || $order->status == 1) {
					return true;
				}
				if ($message["return_code"] === "SUCCESS" && $message["result_code"] === "SUCCESS") {
					$this->handle($message["out_trade_no"], $message["total_fee"]);
					return true;
				}
			});
		}
	}
	public function businessVipPay()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		$vipMoney = $this->request->post("money");
		if (!$vipMoney) {
			return $this->ajaxReturn($this->errorCode, "缺少金额参数");
		}
		$vipType = $this->request->post("type");
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "wxadmin_name" => $uid])->value("business_id");
		$orderSn = "P" . date("YmdHis") . rand(1000, 9999);
		$data = ["order_sn" => $orderSn, "money" => $vipMoney, "status" => 0, "createtime" => time(), "vip_type" => $vipType, "wxapp_id" => $wxapp_id, "s_id" => $s_id, "u_id" => $uid, "business_id" => $businessId];
		$r = \think\facade\Db::name("zh_vip_order")->insert($data);
		if ($r) {
			try {
				$notify_url = "http://" . $this->request->host() . "/api/ZhBusiness/payResult/wxapp_id/" . $wxapp_id;
				$js_pay = \app\api\service\PaymentService::instance($wxapp_id)->create($user["openid"], $orderSn, $vipMoney, "会员充值", ["notify_url" => $notify_url]);
				return $this->ajaxReturn($this->successCode, "操作成功", ["paydata" => $js_pay]);
			} catch (\Exception $e) {
				return $this->ajaxReturn($this->errorCode, $e->getMessage());
			}
		}
	}
	public function handle($ordersn, $fee)
	{
		$order = \think\facade\Db::name("zh_vip_order")->where(["order_sn" => $ordersn])->find();
		$data = ["status" => 1, "pay_time" => time()];
		if ($order["status"] == 0 && intval(bcmul($order["money"], 100)) == $fee) {
			\think\facade\Db::name("zh_vip_order")->where(["order_sn" => $ordersn])->update($data);
			$where = ["business_id" => $order["business_id"]];
			$insert = ["wxapp_id" => $order["wxapp_id"], "bus_id" => $order["business_id"], "o_id" => $order["id"], "type" => 1, "number" => $order["number"]];
			\think\facade\Db::name("business_account_log")->insert($insert);
			\think\facade\Db::name("zh_business")->where($where)->inc("balance", $order["number"])->update();
		}
	}
	public function payResult()
	{
		$wxapp_id = input("wxapp_id");
		trace("进入回调" . $wxapp_id, "notify_" . date("Y_m_d"));
		$app = \app\api\service\PaymentService::instance($wxapp_id)::getPayApp();
		$response = $app->handlePaidNotify(function ($message, $fail) {
			trace($message, "notify_" . date("Y_m_d"));
			$order = \think\facade\Db::name("zh_vip_order")->where(["order_sn" => $message["out_trade_no"]])->find();
			if (!$order || $order->status == 1) {
				return true;
			}
			if ($message["return_code"] === "SUCCESS") {
				if ($message["result_code"] === "SUCCESS") {
					$data = ["status" => 1, "pay_time" => time(), "d_order_sn" => $message["transaction_id"]];
				}
			} else {
				return $fail("通信失败，请稍后再通知我");
			}
			\think\facade\Db::name("zh_vip_order")->where(["order_sn" => $message["out_trade_no"]])->update($data);
			$where = ["business_id" => $order["business_id"]];
			$businessData = \think\facade\Db::name("zh_business")->where($where)->find();
			if ($order["vip_type"] == 1) {
				$businessData["expire_time"] = strtotime(" +7day", $businessData["expire_time"]);
				\think\facade\Db::name("zh_business")->where($where)->update(["expire_time" => $businessData["expire_time"]]);
			} elseif ($order["vip_type"] == 2) {
				$businessData["expire_time"] = strtotime(" +1month", $businessData["expire_time"]);
				\think\facade\Db::name("zh_business")->where($where)->update(["expire_time" => $businessData["expire_time"]]);
			}
			return true;
		});
		return $response->send();
	}
}