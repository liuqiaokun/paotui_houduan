<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller;

class Account extends Admin
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
			$where["account"] = $this->request->param("account", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "wxapp_id,account,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "wxapp_id desc";
			$res = \app\gcadmin\service\AccountService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "account,pwd,create_time";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\AccountService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$wxapp_id = $this->request->get("wxapp_id", "", "serach_in");
			if (!$wxapp_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\gcadmin\model\Account::find($wxapp_id)));
			return view("update");
		} else {
			$postField = "wxapp_id,account,pwd";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\AccountService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
}