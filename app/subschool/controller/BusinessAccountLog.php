<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class BusinessAccountLog extends Admin
{
	public function index()
	{
		if (!$this->request->isAjax()) {
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$data = \think\facade\Db::name("business_account_log")->where("s_id", 0)->select();
			if (count($data) > 0) {
				foreach ($data as &$v) {
					$bus = \think\facade\Db::name("zh_business")->find($v["bus_id"]);
					\think\facade\Db::name("business_account_log")->where("id", $v["id"])->update(["s_id" => $bus["s_id"]]);
				}
			}
			$where = [];
			$where["wxapp_id"] = session("subschool.wxapp_id");
			$where["s_id"] = session("subschool.s_id");
			$where["type"] = $this->request->param("type", "", "serach_in");
			$where["bus_id"] = $this->request->param("bus_id", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "*";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$business = \think\facade\Db::name("zh_business")->where(["wxapp_id" => session("subschool.wxapp_id"), "s_id" => session("subschool.s_id")])->select();
			foreach ($business as &$v) {
				$bus_array[$v["business_id"]] = $v["business_name"];
			}
			$res = \app\subschool\service\BusinessAccountLogService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			foreach ($res["rows"] as &$v) {
				$v["business_name"] = $bus_array[$v["bus_id"]];
				$v["remark"] = $v["type"] == 0 ? "下单扣除" : ($v["o_id"] == 99999999 ? "系统后台充值" : "用户充值");
			}
			return json($res);
		}
	}
}