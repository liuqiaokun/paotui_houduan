<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class MobileRechargeOrder extends Admin
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
			$where["a.wxapp_id"] = session("subschool.wxapp_id");
			$where["a.s_id"] = session("subschool.s_id");
			$where["a.operator"] = $this->request->param("operator", "", "serach_in");
			$where["a.mobile"] = $this->request->param("mobile", "", "serach_in");
			$where["a.status"] = $this->request->param("status", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.nickname";
			$orderby = $sort && $order ? $sort . " " . $order : "oid desc";
			$res = \app\subschool\service\MobileRechargeOrderService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$oid = $this->request->get("oid", "", "serach_in");
			if (!$oid) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\MobileRechargeOrder::find($oid)));
			return view("update");
		} else {
			$postField = "oid,status";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\MobileRechargeOrderService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
}