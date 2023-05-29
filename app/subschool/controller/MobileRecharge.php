<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class MobileRecharge extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete"])) {
			$idx = $this->request->param("mid", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\subschool\model\MobileRecharge::find($v);
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
			$field = "mid,price_val,price,createtime";
			$orderby = $sort && $order ? $sort . " " . $order : "mid desc";
			$res = \app\subschool\service\MobileRechargeService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "wxapp_id,s_id,price_val,price,createtime";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\MobileRechargeService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$mid = $this->request->get("mid", "", "serach_in");
			if (!$mid) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\MobileRecharge::find($mid)));
			return view("update");
		} else {
			$postField = "mid,wxapp_id,s_id,price_val,price";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\MobileRechargeService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("mid", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\MobileRecharge::destroy(["mid" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}