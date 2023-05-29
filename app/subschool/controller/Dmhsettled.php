<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Dmhsettled extends Admin
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
			$where["name"] = $this->request->param("name_s", "", "serach_in");
			$where["phone"] = $this->request->param("phone", "", "serach_in");
			$where["state"] = $this->request->param("state", "", "serach_in");
			$where = [];
			$where["wxapp_id"] = session("subschool.wxapp_id");
			$where["s_id"] = session("subschool.s_id");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "d_id,name,phone,address,image,type_id,start,end,qualifications,state,createtime,paytime,pay";
			$orderby = $sort && $order ? $sort . " " . $order : "d_id desc";
			$res = \app\subschool\service\DmhsettledService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function updateExt()
	{
		$postField = "d_id,paystate";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["d_id"]) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\Dmhsettled::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$d_id = $this->request->get("d_id", "", "serach_in");
			if (!$d_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\Dmhsettled::find($d_id)));
			return view("update");
		} else {
			$postField = "d_id,state";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$s_id = session("subschool.s_id");
			if ($data["state"] == 2) {
				$this->refuse($data["d_id"]);
			}
			if ($data["state"] == 1) {
				$this->adopt($data["d_id"]);
			}
			$res = \app\subschool\service\DmhsettledService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function refuse($d_id)
	{
		$where["d_id"] = $d_id;
		$order = $this->app->db->name("dmh_settled")->where($where)->find();
		if (!$order) {
			return $this->ajaxReturn($this->errorCode, "订单查找失败");
		}
		$s_id = session("subschool.s_id");
		$wxapp_id = session("subschool.wxapp_id");
		$school = $this->app->db->name("school")->where("s_id", $s_id)->find();
		if ($school["charge_mode"] == 1) {
			$res = $this->app->db->name("dmh_settled")->where($where)->update(["paystate" => 0, "state" => 2]);
			\app\api\service\PaymentService::instance($wxapp_id)->refund($order["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $order["pay"], $order["pay"], $order["pay_type"]);
		} else {
			$res = $this->app->db->name("dmh_settled")->where($where)->update(["paystate" => 0, "state" => 2]);
		}
	}
	public function adopt($d_id)
	{
		$where["d_id"] = $d_id;
		$order = $this->app->db->name("dmh_settled")->where($where)->find();
		$type = $this->app->db->name("zh_business_type")->where("type_id", $order["type_id"])->find();
		$data = ["s_id" => $order["s_id"], "wxapp_id" => $order["wxapp_id"], "type_id" => $order["type_id"], "wxadmin_name" => $order["u_id"], "type_name" => $type["type_name"], "start_time" => $order["start"], "end_time" => $order["end"], "business_name" => $order["name"], "business_address" => $order["address"], "business_image" => $order["image"], "phone" => $order["phone"], "longitude" => $order["longitude"], "latitude" => $order["latitude"], "type" => 1, "status" => 1, "createtime" => time(), "starting_fee" => 0, "is_recommend" => 0];
		$res = $this->app->db->name("zh_business")->insert($data);
	}
}