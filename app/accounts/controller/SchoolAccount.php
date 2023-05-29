<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class SchoolAccount extends Admin
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
			$where["a.wxapp_id"] = session("accounts.wxapp_id");
			$where["a.s_id"] = $this->request->param("s_id", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.s_name";
			$orderby = $sort && $order ? $sort . " " . $order : "a_id desc";
			$res = \app\accounts\service\SchoolAccountService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "status,pwd,s_id,account,create_time,wxapp_id";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\SchoolAccountService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$a_id = $this->request->get("a_id", "", "serach_in");
			if (!$a_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\accounts\model\SchoolAccount::find($a_id)));
			return view("update");
		} else {
			$postField = "a_id,s_id,account,pwd,status,wxapp_id";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\SchoolAccountService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("a_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\SchoolAccount::destroy(["a_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}