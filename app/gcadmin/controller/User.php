<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller;

class User extends Admin
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
			$where["a.user"] = $this->request->param("user", "", "serach_in");
			$where["a.role_id"] = ["find in set", $this->request->param("role_id", "", "serach_in")];
			$where["a.status"] = $this->request->param("status", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "";
			$orderby = $sort && $order ? $sort . " " . $order : "user_id desc";
			$sql = "select a.*,group_concat(b.name) as role_name from pre_user as a left join pre_role as b on find_in_set(b.role_id,a.role_id)  group by a.user_id";
			$limit = ($page - 1) * $limit . "," . $limit;
			$res = \xhadmin\CommonService::loadList($sql, formatWhere($where), $limit, $orderby);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "name,user,pwd,role_id,note,status,create_time,avatar";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\UserService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$user_id = $this->request->get("user_id", "", "serach_in");
			if (!$user_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\gcadmin\model\User::find($user_id)));
			return view("update");
		} else {
			$postField = "user_id,name,user,role_id,note,status,create_time,avatar";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\UserService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function updatePassword()
	{
		if (!$this->request->isPost()) {
			$info["user_id"] = $this->request->get("user_id", "", "serach_in");
			return view("updatePassword", ["info" => $info]);
		} else {
			$postField = "user_id,pwd";
			$data = $this->request->only(explode(",", $postField), "post", null);
			if (empty($data["user_id"])) {
				$this->error("参数错误");
			}
			\app\gcadmin\service\UserService::updatePassword($data);
			return json(["status" => "00", "msg" => "操作成功"]);
		}
	}
	public function updateExt()
	{
		$postField = "user_id,status";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["user_id"]) {
			$this->error("参数错误");
		}
		try {
			\app\gcadmin\model\User::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function delete()
	{
		$idx = $this->request->post("user_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		if (in_array("1", explode(",", $idx))) {
			$this->error("该用户禁止删除");
		}
		\app\gcadmin\model\User::destroy(["user_id" => explode(",", $idx)]);
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}