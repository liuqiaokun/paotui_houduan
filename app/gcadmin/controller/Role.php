<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller;

class Role extends Admin
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
			$field = "*";
			$orderby = $sort && $order ? $sort . " " . $order : "role_id desc";
			$res = \app\gcadmin\service\RoleService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			$res["rows"] = formartList(["role_id", "pid", "name", "name"], $res["rows"]);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "pid,name,status,description";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\RoleService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$role_id = $this->request->get("role_id", "", "serach_in");
			if (!$role_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\gcadmin\model\Role::find($role_id)));
			return view("update");
		} else {
			$postField = "role_id,pid,name,status,description";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\RoleService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function updateExt()
	{
		$postField = "role_id,status";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["role_id"]) {
			$this->error("参数错误");
		}
		try {
			\app\gcadmin\model\Role::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function delete()
	{
		$idx = $this->request->post("role_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		if ($idx == 1) {
			$this->error("该角色禁止删除");
		}
		if (\app\gcadmin\model\Role::where("pid", $idx)->find()) {
			$this->error("请先删除子角色");
		}
		try {
			\app\gcadmin\model\Role::destroy(["role_id" => explode(",", $idx)]);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}