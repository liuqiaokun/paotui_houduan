<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller;

class Dates extends Admin
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
			$where["a.t_id"] = ["like", $this->request->param("t_id", "", "serach_in")];
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["a.createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.title";
			$orderby = $sort && $order ? $sort . " " . $order : "d_id desc";
			$res = \app\gcadmin\service\DatesService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "date,t_id,createtime";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\DatesService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
}