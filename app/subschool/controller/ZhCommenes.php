<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class ZhCommenes extends Admin
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
			$where["a.s_id"] = session("subschool.s_id");
			$where["a.wxapp_id"] = session("subschool.wxapp_id");
			$where["a.article_id"] = $this->request->param("article_id", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["a.createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.content";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\ZhCommenesService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhCommenes::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}