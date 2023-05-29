<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class WechatUser extends Admin
{
	public function index()
	{
		$school = \think\facade\Db::name("school")->where("wxapp_id", session("accounts.wxapp_id"))->select();
		if (!$this->request->isAjax()) {
			$this->view->assign("schoolList", $school);
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["a.wxapp_id"] = session("accounts.wxapp_id");
			$where["a.nickname"] = ["like", $this->request->param("nickname", "", "serach_in")];
			$where["a.s_id"] = $this->request->param("s_id", "", "serach_in");
			$where["a.run_status"] = $this->request->param("run_status", "", "serach_in");
			$where["a.t_name"] = $this->request->param("t_name", "", "serach_in");
			$where["a.phone"] = $this->request->param("phone", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.s_name";
			$orderby = $sort && $order ? $sort . " " . $order : "u_id desc";
			foreach ($school as &$v) {
				$schoolList[$v["s_id"]] = $v["s_name"];
			}
			$res = \app\accounts\service\WechatUserService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			foreach ($res["rows"] as &$v) {
				$v["auth_sname"] = $v["auth_sid"] ? $schoolList[$v["auth_sid"]] : "";
			}
			$this->view->assign("schoolList", $school);
			return json($res);
		}
	}
	public function updateExt()
	{
		$idx = $this->request->post("u_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\WechatUser::where(["u_id" => explode(",", $idx)])->update(["is_runner,run_status" => "600px|400px"]);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$u_id = $this->request->get("u_id", "", "serach_in");
			if (!$u_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\accounts\model\WechatUser::find($u_id)));
			$school = \think\facade\Db::name("school")->where("wxapp_id", session("accounts.wxapp_id"))->select();
			$this->view->assign("schoolList", $school);
			return view("update");
		} else {
			$postField = "u_id,run_status,t_sex,auth_sid,phone,t_name,is_black";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\WechatUserService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
}