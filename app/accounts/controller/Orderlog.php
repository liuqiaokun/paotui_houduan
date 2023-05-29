<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Orderlog extends Admin
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
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,o_id,u_id,status,addtime";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\accounts\service\OrderlogService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			$list = [];
			foreach ($res["rows"] as $k => $v) {
				$list[$k] = $v;
				if ($v["status"] == 1) {
					$list[$k]["status"] = "修改订单为未支付";
				}
				if ($v["status"] == 2) {
					$list[$k]["status"] = "修改订单为待接单";
				}
				if ($v["status"] == 3) {
					$list[$k]["status"] = "修改订单为待取货";
				}
				if ($v["status"] == 4) {
					$list[$k]["status"] = "修改订单为已完成";
				}
				if ($v["status"] == 5) {
					$list[$k]["status"] = "修改订单为已过期";
				}
				if ($v["status"] == 6) {
					$list[$k]["status"] = "修改订单为未完成";
				}
				if ($v["status"] == 7) {
					$list[$k]["status"] = "修改订单为待送达";
				}
				if ($v["status"] == 8) {
					$list[$k]["status"] = "修改订单为已取消";
				}
				if ($v["status"] == 9) {
					$list[$k]["status"] = "修改订单为等待取消抢单";
				}
				if ($v["status"] == 10) {
					$list[$k]["status"] = "修改订单为待到店";
				}
				if ($v["status"] == 11) {
					$list[$k]["status"] = "修改订单为已送达";
				}
			}
			$res["rows"] = $list;
			return json($res);
		}
	}
}