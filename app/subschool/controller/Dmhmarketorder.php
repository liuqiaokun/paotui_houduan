<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Dmhmarketorder extends Admin
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
			$where["oid"] = $this->request->param("oid", "", "serach_in");
			$where["paystate"] = $this->request->param("paystate", "", "serach_in");
			$where["s_id"] = session("subschool.s_id");
			$pay_time_start = $this->request->param("pay_time_start", "", "serach_in");
			$pay_time_end = $this->request->param("pay_time_end", "", "serach_in");
			$where["pay_time"] = ["between", [strtotime($pay_time_start), strtotime($pay_time_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,oid,wxapp_id,uid,pay,branch,state,phone,name,other,create_time,pay_time,purchase,paystate,reason,commission";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\DmhmarketorderService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			$goods = [];
			foreach ($res["rows"] as $key => $v) {
				$goods[$key] = $v;
				$order = $this->app->db->name("dmh_market_order")->where("oid", $v["oid"])->find();
				$goods[$key]["goods"] = $this->app->db->name("dmh_market_goods")->where("m_id", $order["m_id"])->find();
				if ($v["paystate"] == 0) {
					$goods[$key]["paystatename"] = "未支付";
				}
				if ($v["paystate"] == 1) {
					$goods[$key]["paystatename"] = "已支付";
				}
				if ($v["paystate"] == 2) {
					$goods[$key]["paystatename"] = "已完成";
				}
				if ($v["paystate"] == 3) {
					$goods[$key]["paystatename"] = "退款中";
				}
				if ($v["paystate"] == 4) {
					$goods[$key]["paystatename"] = "退款完成";
				}
			}
			$res["rows"] = $goods;
			return json($res);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$id = $this->request->get("id", "", "serach_in");
			if (!$id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\Dmhmarketorder::find($id)));
			return view("update");
		} else {
			$postField = "id,phone,name";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\DmhmarketorderService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		$order = $this->app->db->name("dmh_market_order")->where("id", $idx)->find();
		$res = $this->app->db->name("dmh_market_order")->where("id", $idx)->update(["paystate" => 4]);
		if ($res) {
			\app\api\service\PaymentService::instance($order["wxapp_id"])->refund($order["oid"], "T" . date("YmdHis") . rand(1000, 9999), $order["pay"], $order["pay"], $order["pay_type"]);
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}