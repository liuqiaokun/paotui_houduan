<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class UploadConfig extends Admin
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
			$where["title"] = $this->request->param("title", "", "serach_in");
			$where["thumb_type"] = $this->request->param("thumb_type", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,title,thumb_status,upload_replace,thumb_width,thumb_height,thumb_type";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\admin\service\UploadConfigService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function updateExt()
	{
		$postField = "id,thumb_status,upload_replace";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["id"]) {
			$this->error("参数错误");
		}
		try {
			\app\admin\model\UploadConfig::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "title,upload_replace,thumb_status,thumb_width,thumb_height,thumb_type";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\admin\service\UploadConfigService::add($data);
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
			$this->view->assign("info", checkData(\app\admin\model\UploadConfig::find($id)));
			return view("update");
		} else {
			$postField = "id,title,upload_replace,thumb_status,thumb_width,thumb_height,thumb_type";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\admin\service\UploadConfigService::update($data);
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
			\app\admin\model\UploadConfig::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}