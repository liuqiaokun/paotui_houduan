<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Dmhmarketcategory extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete"])) {
			$idx = $this->request->param("id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\subschool\model\Dmhmarketcategory::find($v);
					if ($info["wxapp_id"] != session("subschool.wxapp_id")) {
						$this->error("你没有操作权限");
					}
				}
			}
		}
	}
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
			$where["title"] = $this->request->param("title", "", "serach_in");
			$create_time_start = $this->request->param("create_time_start", "", "serach_in");
			$create_time_end = $this->request->param("create_time_end", "", "serach_in");
			$where["create_time"] = ["between", [strtotime($create_time_start), strtotime($create_time_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,sort,title,image,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\DmhmarketcategoryService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "sort,s_id,wxapp_id,title,image,create_time";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\DmhmarketcategoryService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$id = $this->request->get("id", "", "serach_in");
			if (!$id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\Dmhmarketcategory::find($id)));
			return view("update");
		} else {
			$postField = "id,sort,title,image";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\DmhmarketcategoryService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\Dmhmarketcategory::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}