<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class ZhForumClass extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete", "view"])) {
			$idx = $this->request->param("class_id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\subschool\model\ZhForumClass::find($v);
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
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "class_id,sort,class_name,icon,img,createtime,is_recommend,url,is_cate,is_auth_watch";
			$orderby = $sort && $order ? $sort . " " . $order : "class_id desc";
			$res = \app\subschool\service\ZhForumClassService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function updateExt()
	{
		$postField = "class_id,is_recommend,is_cate,is_auth_watch";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["class_id"]) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhForumClass::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "s_id,wxapp_id,sort,class_name,icon,img,createtime,is_recommend,url,is_cate,is_auth_watch";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\ZhForumClassService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$class_id = $this->request->get("class_id", "", "serach_in");
			if (!$class_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\ZhForumClass::find($class_id)));
			return view("update");
		} else {
			$postField = "class_id,class_name,createtime,sort,icon,img,is_recommend,url,is_cate,is_auth_watch";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\ZhForumClassService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("class_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhForumClass::destroy(["class_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function view()
	{
		$class_id = $this->request->get("class_id", "", "serach_in");
		if (!$class_id) {
			$this->error("参数错误");
		}
		$this->view->assign("info", \app\subschool\model\ZhForumClass::find($class_id));
		return view("view");
	}
}