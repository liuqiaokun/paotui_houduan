<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller;

class Version extends Admin
{
	public function index()
	{
		echo 33;
		exit;
		$s_id = session("subschool")["s_id"];
		$school = $this->app->db->name("school")->where("s_id", $s_id)->find();
		$this->view->assign("info", $school);
		return view("index");
	}
	public function update()
	{
		$s_name = input("s_name", "", "trim");
		$school_rate = input("school_rate", "", "trim");
		$robot_key = input("robot_key", "", "trim");
		$step = input("step", "", "trim");
		$alipay_name = input("alipay_name", "", "trim");
		$alipay_account = input("alipay_account", "", "trim");
		$reward = input("reward", "", "trim");
		$s_id = session("subschool")["s_id"];
		$data = ["s_name" => $s_name, "school_rate" => $school_rate, "robot_key" => $robot_key, "step" => $step, "alipay_name" => $alipay_name, "alipay_account" => $alipay_account, "reward" => $reward];
		$school = $this->app->db->name("school")->where("s_id", $s_id)->update($data);
		if ($school) {
			return json(["status" => "00", "msg" => "修改成功"]);
		} else {
			return json(["status" => "00", "msg" => "修改失败"]);
		}
	}
}