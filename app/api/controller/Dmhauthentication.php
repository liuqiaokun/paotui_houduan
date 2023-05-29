<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhauthentication extends Common
{
	public function index()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["name"] = $this->request->get("name", "", "serach_in");
		$where["phone"] = $this->request->get("phone", "", "serach_in");
		$create_time_start = $this->request->get("create_time_start", "", "serach_in");
		$create_time_end = $this->request->get("create_time_end", "", "serach_in");
		$where["create_time"] = ["between", [strtotime($create_time_start), strtotime($create_time_end)]];
		$where["uid"] = $this->request->get("uid", "", "serach_in");
		$where["state"] = $this->request->get("state", "", "serach_in");
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\DmhauthenticationService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function update()
	{
		$postField = "id,name,phone,create_time,uid,state,wxapp_id,s_id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (empty($data["id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["id"] = $data["id"];
		$res = \app\api\service\DmhauthenticationService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function delete()
	{
		$idx = $this->request->post("ids", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["id"] = explode(",", $idx);
		try {
			\app\api\model\Dmhauthentication::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
}