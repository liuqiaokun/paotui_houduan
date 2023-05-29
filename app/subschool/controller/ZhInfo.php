<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class ZhInfo extends Admin
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
			$where["s_id"] = session("subschool.s_id");
			$where["wxapp_id"] = session("subschool.wxapp_id");
			$where["title"] = ["like", $this->request->param("title", "", "serach_in")];
			$where["address"] = ["like", $this->request->param("address", "", "serach_in")];
			$where["type"] = $this->request->param("type", "", "serach_in");
			$where["media_type"] = $this->request->param("media_type", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "info_id,title,address,u_id,type,media_type,createtime";
			$orderby = $sort && $order ? $sort . " " . $order : "info_id desc";
			$res = \app\subschool\service\ZhInfoService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function view()
	{
		$info_id = $this->request->get("info_id", "", "serach_in");
		if (!$info_id) {
			$this->error("参数错误");
		}
		$res = \app\subschool\model\ZhInfo::find($info_id);
		$res["image"] = explode(",", $res["image"]);
		$this->view->assign("info", $res);
		return view("view");
	}
	public function delete()
	{
		$idx = $this->request->post("info_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhInfo::destroy(["info_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}