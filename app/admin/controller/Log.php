<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class Log extends Admin
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
			$where["application_name"] = $this->request->param("application_name", "", "serach_in");
			$where["username"] = $this->request->param("username", "", "serach_in");
			$where["url"] = $this->request->param("url", "", "serach_in");
			$where["ip"] = $this->request->param("ip", "", "serach_in");
			$where["type"] = $this->request->param("type", "", "serach_in");
			$create_time_start = $this->request->param("create_time_start", "", "serach_in");
			$create_time_end = $this->request->param("create_time_end", "", "serach_in");
			$where["create_time"] = ["between", [strtotime($create_time_start), strtotime($create_time_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,application_name,username,url,ip,type,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\admin\service\LogService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\admin\model\Log::destroy(["id" => explode(",", $idx)], true);
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
		$this->view->assign("info", \app\admin\model\Log::find($id));
		return view("view");
	}
	public function dumpData()
	{
		$where = [];
		$where["application_name"] = $this->request->param("application_name", "", "serach_in");
		$where["username"] = $this->request->param("username", "", "serach_in");
		$where["url"] = $this->request->param("url", "", "serach_in");
		$where["ip"] = $this->request->param("ip", "", "serach_in");
		$where["type"] = $this->request->param("type", "", "serach_in");
		$create_time_start = $this->request->param("create_time_start", "", "serach_in");
		$create_time_end = $this->request->param("create_time_end", "", "serach_in");
		$where["create_time"] = ["between", [strtotime($create_time_start), strtotime($create_time_end)]];
		$where["id"] = ["in", $this->request->param("id", "", "serach_in")];
		try {
			$fieldInfo = [];
			for ($j = 0; $j < 100; $j++) {
				$fieldInfo[] = $this->request->param($j);
			}
			$list = \app\admin\model\Log::where(formatWhere($where))->order("id desc")->select();
			if (empty($list)) {
				throw new Exception("没有数据");
			}
			\app\admin\service\LogService::dumpData(htmlOutList($list), filterEmptyArray(array_unique($fieldInfo)));
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
	}
}