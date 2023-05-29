<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller;

class Tickets extends Admin
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
			$where["title"] = ["like", $this->request->param("title", "", "serach_in")];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "t_id,title,img";
			$orderby = $sort && $order ? $sort . " " . $order : "t_id desc";
			$res = \app\gcadmin\service\TicketsService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "title,img,notice";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\TicketsService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$t_id = $this->request->get("t_id", "", "serach_in");
			if (!$t_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\gcadmin\model\Tickets::find($t_id)));
			return view("update");
		} else {
			$postField = "t_id,title,img,notice";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\gcadmin\service\TicketsService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("t_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\gcadmin\model\Tickets::destroy(["t_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}