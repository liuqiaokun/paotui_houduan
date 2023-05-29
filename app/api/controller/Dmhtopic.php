<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhtopic extends Common
{
	public function index()
	{
		$limit = $this->request->post("limit", 10, "intval");
		$page = $this->request->post("page", 1, "intval");
		$title = $this->request->post("title", "", "serach_in");
		$where = [];
		$where["s_id"] = $this->request->post("s_id", "", "serach_in");
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\DmhtopicService::indexList(formatWhere($where), $field, $orderby, $limit, $title, $page);
		$data = [];
		foreach ($res["list"] as $k => $v) {
			$data[$k] = $v;
			$data[$k]["con"] = $this->app->db->name("zh_articles")->where("topic", "like", "%" . $v["id"] . "%")->count();
			$data[$k]["chad"] = false;
		}
		$res["list"] = $data;
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}