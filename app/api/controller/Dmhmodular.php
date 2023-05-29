<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhmodular extends Common
{
	public function index()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["tltle"] = $this->request->get("tltle", "", "serach_in");
		$where["types"] = $this->request->get("types", "", "serach_in");
		$start_start = $this->request->get("start_start", "", "serach_in");
		$start_end = $this->request->get("start_end", "", "serach_in");
		$where["start"] = ["between", [$start_start, $start_end]];
		$ladder_start = $this->request->get("ladder_start", "", "serach_in");
		$ladder_end = $this->request->get("ladder_end", "", "serach_in");
		$where["ladder"] = ["between", [$ladder_start, $ladder_end]];
		$create_time_start = $this->request->get("create_time_start", "", "serach_in");
		$create_time_end = $this->request->get("create_time_end", "", "serach_in");
		$where["create_time"] = ["between", [strtotime($create_time_start), strtotime($create_time_end)]];
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\DmhmodularService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}