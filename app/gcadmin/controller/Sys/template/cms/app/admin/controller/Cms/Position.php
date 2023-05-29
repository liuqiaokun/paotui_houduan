<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Cms;

class Position extends \app\admin\controller\Admin
{
	public function index()
	{
		if (!$this->request->isAjax()) {
			return view("cms/position/index");
		} else {
			$limit = $this->request->post("limit", 0, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where["title"] = $this->request->param("title", "", "serach_in");
			$orderby = "position_id desc";
			$field = "position_id,title";
			$res = \app\admin\service\Cms\PositionService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function updateExt()
	{
		$postField = "position_id,sortid";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["position_id"]) {
			$this->error("参数错误");
		}
		\app\admin\model\Cms\Position::update($data);
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("cms/position/add");
		} else {
			$data = $this->request->post();
			$res = \app\admin\service\Cms\PositionService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$position_id = $this->request->get("position_id", "", "intval");
			if (!$position_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\admin\model\Cms\Position::find($position_id)));
			return view("cms/position/update");
		} else {
			$data = $this->request->post();
			\app\admin\service\Cms\PositionService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("position_ids", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		\app\admin\model\Cms\Position::destroy(["position" => explode(",", $idx)]);
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}