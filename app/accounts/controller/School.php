<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class School extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["update", "delete", "updateExt"])) {
			$idx = $this->request->param("s_id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\accounts\model\School::find($v);
					if ($info["wxapp_id"] != session("accounts.wxapp_id")) {
						$this->error("你没有操作权限");
					}
				}
			}
		}
	}
	public function index()
	{
/* 		$url1 = "http://send.fkynet.net/api/LinkExt/getcode";
		$res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"]);
		if ($res != 666) {
			echo "操作失败";
			exit;
		} */
		if (!$this->request->isAjax()) {
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["s_name"] = $this->request->param("s_name", "", "serach_in");
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$create_time_start = $this->request->param("create_time_start", "", "serach_in");
			$create_time_end = $this->request->param("create_time_end", "", "serach_in");
			$where["create_time"] = ["between", [strtotime($create_time_start), strtotime($create_time_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "s_id,s_name,logo,plat_rate,school_rate,second_rate,reward_rate,store_rate,fx_store_rate,store_bean_switch,status,create_time,is_distribution";
			$orderby = $sort && $order ? $sort . " " . $order : "s_id desc";
			$res = \app\accounts\service\SchoolService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "s_name,logo,province,city,wxapp_id,plat_rate,school_rate,second_rate,fx_second_rate,edit_status,robot_key,step,reward_rate,coupon_list,store_rate,fx_store_rate,bean_rate,deduction_num,store_bean_switch,threshold,status,create_time,charge_mode,charge_price,is_open_pay";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\SchoolService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
	if (!$this->request->isPost()) {
			$s_id = $this->request->get("s_id", "", "serach_in");
			if (!$s_id) {
				$this->error("参数错误");
			}
//			 $url1 = "http://send.fkynet.net/api/LinkExt/getcheck";
//			$getres = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&s_id=" . $s_id);
//			$getres = json_decode($getres, true);
//			if ($getres["status"] == 666) {
            $info = \think\facade\Db::name("school")->where('s_id',$s_id)->find();
//				$info = \think\facade\Db::query("")[0];
//			} else {
//				return json(["status" => "01", "msg" => "该域名未授权"]);
//			}
			$this->view->assign("info", $info);
			return view("update");
		} else {
			$postField = "s_id,s_name,logo,province,city,plat_rate,school_rate,second_rate,fx_second_rate,edit_status,robot_key,step,reward_rate,coupon_list,store_rate,fx_store_rate,bean_rate,deduction_num,store_bean_switch,threshold,status,charge_mode,charge_price,is_open_pay";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\SchoolService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("s_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\School::destroy(["s_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function updateExt()
	{
		$postField = "s_id,store_bean_switch,status,is_distribution";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["s_id"]) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\School::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function updateProject()
	{
		if (!$this->request->isPost()) {
			$s_id = $this->request->get("s_id", "", "serach_in");
			if (!$s_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\accounts\model\School::find($s_id)));
			return view("updateProject");
		} else {
			$postField = "s_id,is_distribution,level1,level2,project";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\SchoolService::updateProject($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function hide()
	{
		$idx = $this->request->post("s_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\think\facade\Db::name("school")->where("s_id", "in", explode(",", $idx))->update(["status" => 0]);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function show()
	{
		$idx = $this->request->post("s_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\think\facade\Db::name("school")->where("s_id", "in", explode(",", $idx))->update(["status" => 1]);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}