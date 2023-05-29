<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class ZhBusinesType extends Admin
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
			$where["type_name"] = $this->request->param("type_name", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "type_id,type_name,createtime,type_image,sort";
			$orderby = $sort && $order ? $sort . " " . $order : "type_id desc";
			$res = \app\subschool\service\ZhBusinesTypeService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "type_name,createtime,type_image,sort";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$data["s_id"] = session("subschool.s_id");
			$data["wxapp_id"] = session("subschool.wxapp_id");
			$res = \app\subschool\service\ZhBusinesTypeService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$type_id = $this->request->get("type_id", "", "serach_in");
			if (!$type_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\ZhBusinesType::find($type_id)));
			return view("update");
		} else {
			$postField = "type_id,type_name,createtime,type_image,sort";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\ZhBusinesTypeService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("type_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhBusinesType::destroy(["type_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function view()
	{
		$type_id = $this->request->get("type_id", "", "serach_in");
		if (!$type_id) {
			$this->error("参数错误");
		}
		$this->view->assign("info", \app\subschool\model\ZhBusinesType::find($type_id));
		return view("view");
	}
}