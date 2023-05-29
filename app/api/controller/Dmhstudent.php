<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhstudent extends Common
{
	public function index()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["name"] = $this->request->get("name", "", "serach_in");
		$where["phone"] = $this->request->get("phone", "", "serach_in");
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\DmhstudentService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$uid = $this->request->uid;
		$postField = "name,phone,remarks,student,images,s_id,wxapp_id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (empty($data["name"])) {
			return $this->ajaxReturn($this->errorCode, "姓名不能为空");
		}
		if (empty($data["images"])) {
			return $this->ajaxReturn($this->errorCode, "请上传证件照");
		}
		$preg_phone = "/^1[345789]\\d{9}\$/ims";
		$phone = trim(input("phone"));
		if (!preg_match($preg_phone, $phone)) {
			return $this->ajaxReturn($this->errorCode, "请输入正确的手机号");
		}
		$data["u_id"] = $uid;
		$data["create_time"] = time();
		$data["state"] = 0;
		$student = $this->app->db->name("dmh_student")->where("u_id", $data["u_id"])->find();
		if ($student) {
			$res = $this->app->db->name("dmh_student")->where("u_id", $data["u_id"])->update($data);
		} else {
			$res = \app\api\service\DmhstudentService::add($data);
		}
		if ($res) {
			return $this->ajaxReturn($this->successCode, "申请成功");
		} else {
			return $this->ajaxReturn($this->errorCode, "申请失败");
		}
	}
	public function update()
	{
		$postField = "id,name,phone,remarks,student,create_time,images,u_id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (empty($data["id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["id"] = $data["id"];
		$res = \app\api\service\DmhstudentService::update($where, $data);
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
			\app\api\model\Dmhstudent::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function view()
	{
		$uid = $this->request->uid;
		$res = $this->app->db->name("dmh_student")->where("u_id", $uid)->find();
		if ($res) {
			$student["state"] = $res["state"];
		} else {
			$student["state"] = 2;
		}
		return $this->ajaxReturn($this->successCode, "返回成功", $student);
	}
	public function views()
	{
		$uid = $this->request->uid;
		$res = $this->app->db->name("dmh_student")->where("u_id", $uid)->find();
		if ($res) {
			$student["state"] = $res["state"];
		} else {
			$student["state"] = -1;
		}
		return $this->ajaxReturn($this->successCode, "返回成功", $student);
	}
	public function datas()
	{
		$uid = $this->request->uid;
		$res = $this->app->db->name("dmh_student")->where("u_id", $uid)->find();
		if ($res) {
			return $this->ajaxReturn($this->successCode, "返回成功", $res);
		} else {
			return $this->ajaxReturn($this->successCode, "返回失败");
		}
	}
}