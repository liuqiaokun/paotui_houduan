<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Dmhstudent extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete", "view"])) {
			$idx = $this->request->param("id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\subschool\model\Dmhstudent::find($v);
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
			$where["name"] = $this->request->param("name_s", "", "serach_in");
			$where["phone"] = $this->request->param("phone", "", "serach_in");
			$where["s_id"] = session("subschool.s_id");
			$where["wxapp_id"] = session("subschool.wxapp_id");
			$where["state"] = $this->request->param("state", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,name,phone,remarks,reason,student,create_time,images,state";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\DmhstudentService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "name,phone,remarks,student,create_time,images,u_id,s_id,wxapp_id,state";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\DmhstudentService::add($data);
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
			$this->view->assign("info", checkData(\app\subschool\model\Dmhstudent::find($id)));
			return view("update");
		} else {
			$postField = "id,name,phone,remarks,reason,student,create_time,images,u_id,s_id,wxapp_id,state";
			$data = $this->request->only(explode(",", $postField), "post", null);
			if ($data["state"] == 1) {
				$student = $this->app->db->name("dmh_student")->where("id", $data["id"])->find();
				$user = $this->app->db->name("wechat_user")->where("u_id", $student["u_id"])->update(["phone" => $student["phone"], "t_name" => $student["name"]]);
			}
			$res = \app\subschool\service\DmhstudentService::update($data);
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
			\app\subschool\model\Dmhstudent::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function view()
	{
		$id = $this->request->get("id", "", "serach_in");
		if (!$id) {
			$this->error("参数错误");
		}
		$this->view->assign("info", \app\subschool\model\Dmhstudent::find($id));
		return view("view");
	}
}