<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Dmhexpress extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete"])) {
			$idx = $this->request->param("id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\subschool\model\Dmhexpress::find($v);
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
			$where["address"] = $this->request->param("address", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,title,address,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\DmhexpressService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "s_id,wxapp_id,title,address,create_time,client_id,client_secret,machine_code";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\DmhexpressService::add($data);
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
			$this->view->assign("info", checkData(\app\subschool\model\Dmhexpress::find($id)));
			return view("update");
		} else {
			$postField = "id,s_id,wxapp_id,title,address,client_id,client_secret,machine_code";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\DmhexpressService::update($data);
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
			\app\subschool\model\Dmhexpress::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}