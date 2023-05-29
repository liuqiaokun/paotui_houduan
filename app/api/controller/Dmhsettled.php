<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhsettled extends Common
{
	public function index()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["name"] = $this->request->get("name", "", "serach_in");
		$where["phone"] = $this->request->get("phone", "", "serach_in");
		$where["longitude"] = $this->request->get("longitude", "", "serach_in");
		$where["latitude"] = $this->request->get("latitude", "", "serach_in");
		$where["address"] = $this->request->get("address", "", "serach_in");
		$where["type_id"] = $this->request->get("type_id", "", "serach_in");
		$where["start"] = $this->request->get("start", "", "serach_in");
		$where["end"] = $this->request->get("end", "", "serach_in");
		$where["state"] = $this->request->get("state", "", "serach_in");
		$createtime_start = $this->request->get("createtime_start", "", "serach_in");
		$createtime_end = $this->request->get("createtime_end", "", "serach_in");
		$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
		$field = "*";
		$orderby = "d_id desc";
		$res = \app\api\service\DmhsettledService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		if (!input("name")) {
			return $this->ajaxReturn($this->errorCode, "姓名不能为空");
		}
		$preg_phone = "/^1[345789]\\d{9}\$/ims";
		$phone = trim(input("phone"));
		if (!preg_match($preg_phone, $phone)) {
			return $this->ajaxReturn($this->errorCode, "请输入正确的手机号");
		}
		if (!input("image") || input("image") == "undefined") {
			return $this->ajaxReturn($this->errorCode, "请上传店铺门头");
		}
		if (!input("qualifications") || input("qualifications") == undefined) {
			return $this->ajaxReturn($this->errorCode, "请上传店铺资质");
		}
		if (!input("type_id")) {
			return $this->ajaxReturn($this->errorCode, "请选择商家分类");
		}
		if (!input("address") || !input("longitude") || !input("latitude")) {
			return $this->ajaxReturn($this->errorCode, "请选择地址");
		}
		if (!input("start")) {
			return $this->ajaxReturn($this->errorCode, "请选择开始时间");
		}
		if (!input("end")) {
			return $this->ajaxReturn($this->errorCode, "请选择结束时间");
		}
		$postField = "name,phone,openid,s_id,wxapp_id,longitude,latitude,address,image,type_id,start,end,qualifications,state,createtime,updatetime";
		$data = $this->request->only(explode(",", $postField), "get", null);
		$peiz = \think\facade\Db::name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
		$data["ordersn"] = $ordersn = "DMHSTTLED" . rand(0, 9) . time() . rand(0, 9) . rand(0, 9);
		$uid = $this->request->uid;
		$users = $this->app->db->name("wechat_user")->where(["u_id" => $uid])->find();
		$data["createtime"] = time();
		$data["state"] = 3;
		$data["u_id"] = $uid;
		$data["openid"] = $users["openid"];
		$insert["pay_type"] = $peiz["pay_type"];
		$school = $this->app->db->name("school")->where("s_id", input("s_id"))->find();
		if ($school["charge_mode"] == 1) {
			$data["pay"] = $school["charge_price"];
		} else {
			$data["pay"] = 0.0;
		}
		$settled = $this->app->db->name("dmh_settled")->where(["openid" => $data["openid"], "wxapp_id" => $data["wxapp_id"]])->find();
		if ($settled) {
			$res = $this->app->db->name("dmh_settled")->where(["d_id" => $settled["d_id"]])->update($data);
		} else {
			$res = $this->app->db->name("dmh_settled")->insertGetId($data);
		}
		if ($res) {
			if ($school["charge_mode"] == 1) {
				try {
					$notify_url = "http://" . $this->request->host() . "/api/Dmhsettled/payResult/wxapp_id/" . $data["wxapp_id"];
					$js_pay = \app\api\service\PaymentService::instance($data["wxapp_id"])->create($users["openid"], $data["ordersn"], $school["charge_price"], "下单", $peiz["pay_type"], ["notify_url" => $notify_url]);
					return $this->ajaxReturn($this->successCode, "下单成功", $js_pay);
				} catch (\Exception $e) {
					return $this->ajaxReturn($this->errorCode, $e->getMessage());
				}
			} else {
				$this->app->db->name("dmh_settled")->where(["openid" => $data["openid"], "s_id" => $data["s_id"], "wxapp_id" => $data["wxapp_id"]])->update(["paystate" => 1, "state" => 0]);
				return $this->ajaxReturn($this->successCode, "操作成功");
			}
		} else {
			return $this->ajaxReturn($this->errorCode, "提交失败");
		}
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
				$order = $this->app->db->name("dmh_settled")->where("ordersn", $getData["outTradeNo"])->find();
				trace("进入回调7887" . $order["wxapp_id"] . "==" . $getData["outTradeNo"], "notify_" . date("Y_m_d"));
				if ($order["paystate"] == 0) {
					$this->app->db->name("dmh_settled")->where("ordersn", $getData["outTradeNo"])->update(["paystate" => 1, "paytime" => time(), "state" => 0]);
				}
			}
		} else {
			$app = \app\api\service\PaymentService::instance($wxapp_id)::getPayApp();
			$response = $app->handlePaidNotify(function ($message, $fail) {
				trace($message, "notify_" . date("Y_m_d"));
				$order = $this->app->db->name("dmh_settled")->where("ordersn", $message["out_trade_no"])->find();
				trace("进入回调7887" . $order["wxapp_id"] . "==" . $message["out_trade_no"], "notify_" . date("Y_m_d"));
				if (!$order || $order->status == 2) {
					return true;
				}
				if ($message["return_code"] === "SUCCESS") {
					if ($message["result_code"] === "SUCCESS" && $order["paystate"] == 0) {
						$this->app->db->name("dmh_settled")->where("ordersn", $message["out_trade_no"])->update(["paystate" => 1, "paytime" => time(), "state" => 0]);
					}
				} else {
					return $fail("通信失败，请稍后再通知我");
				}
				return true;
			});
			return $response->send();
		}
	}
	public function schoolpay()
	{
		$school = $this->app->db->name("school")->where("s_id", input("s_id"))->find();
		if ($school) {
			return $this->ajaxReturn($this->successCode, "操作成功", ["data" => $school]);
		} else {
			return $this->ajaxReturn($this->successCode, "请求不到该学校", []);
		}
	}
	public function update()
	{
		$postField = "d_id,name,phone,longitude,latitude,address,image,type_id,start,end,qualifications,state,createtime,updatetime";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (empty($data["d_id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["d_id"] = $data["d_id"];
		$res = \app\api\service\DmhsettledService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function delete()
	{
		$idx = $this->request->post("d_ids", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["d_id"] = explode(",", $idx);
		try {
			\app\api\model\Dmhsettled::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function view()
	{
		$data["800px"] = $this->request->get("800px", "", "serach_in");
		$data["100%"] = $this->request->get("100%", "", "serach_in");
		$field = "d_id,name,phone,longitude,latitude,address,image,type_id,start,end,qualifications,state,createtime,updatetime";
		$res = checkData(\app\api\model\Dmhsettled::field($field)->where($data)->find());
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
}