<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Config extends Admin
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
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "";
			$orderby = $sort && $order ? $sort . " " . $order : " desc";
			$res = \app\subschool\service\ConfigService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
}