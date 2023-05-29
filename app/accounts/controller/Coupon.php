<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Coupon extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["update", "updateExt", "delete"])) {
			$idx = $this->request->param("o_id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\accounts\model\Coupon::find($v);
					if ($info["wxapp_id"] != session("accounts.wxapp_id")) {
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
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$where["c_name"] = $this->request->param("c_name", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "o_id,c_name,price,create_time,status";
			$orderby = $sort && $order ? $sort . " " . $order : "o_id desc";
			$res = \app\accounts\service\CouponService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "create_time,price,c_name,wxapp_id,status,type,cut_num";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\CouponService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$o_id = $this->request->get("o_id", "", "serach_in");
			if (!$o_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\accounts\model\Coupon::find($o_id)));
			return view("update");
		} else {
			$postField = "o_id,wxapp_id,c_name,price,status,type,cut_num";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\CouponService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function updateExt()
	{
		$postField = "o_id,status";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["o_id"]) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\Coupon::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function delete()
	{
		$idx = $this->request->post("o_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\Coupon::destroy(["o_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}