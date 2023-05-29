<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class WechatUser extends Admin
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
			$where["a.nickname"] = $this->request->param("nickname", "", "serach_in");
			$where["a.s_id"] = session("subschool.s_id");
			$where["a.t_name"] = $this->request->param("t_name", "", "serach_in");
			$where["a.phone"] = $this->request->param("phone", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.s_name";
			$orderby = $sort && $order ? $sort . " " . $order : "u_id desc";
			$res = \app\subschool\service\WechatUserService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
}