<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class StoreRecharge extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete"])) {
			$idx = $this->request->param("id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\subschool\model\StoreRecharge::find($v);
					if ($info["s_id"] != session("subschool.s_id")) {
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
			$where["wxapp_id"] = session("subschool.wxapp_id");
			$where["s_id"] = session("subschool.s_id");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,price,number,addtime";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\StoreRechargeService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "number,price,s_id,wxapp_id,addtime";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\StoreRechargeService::add($data);
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
			$this->view->assign("info", checkData(\app\subschool\model\StoreRecharge::find($id)));
			return view("update");
		} else {
			$postField = "id,wxapp_id,s_id,price,number";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\StoreRechargeService::update($data);
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
			\app\subschool\model\StoreRecharge::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}