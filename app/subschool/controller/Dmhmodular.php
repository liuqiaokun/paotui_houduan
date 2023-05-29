<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Dmhmodular extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete"])) {
			$idx = $this->request->param("id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\subschool\model\Dmhmodular::find($v);
					if ($info["wxapp_id"] != session("subschool.wxapp_id")) {
						$this->error("你没有操作权限");
					}
				}
			}
		}
	}
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
			$where["s_id"] = session("subschool.s_id");
			$where["wxapp_id"] = session("subschool.wxapp_id");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,title,sort,image,types,start,ladder,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\DmhmodularService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			$discount = $this->discount();
			$this->view->assign("discount", $discount);
			return view("add");
		} else {
			$postField = "title,image,types,start,ladder,create_time,s_id,wxapp_id,appid,sort";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\DmhmodularService::add($data);
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
			$discount = $this->discount();
			$this->view->assign("discount", $discount);
			$this->view->assign("info", checkData(\app\subschool\model\Dmhmodular::find($id)));
			return view("update");
		} else {
			$postField = "id,title,sort,image,types,start,ladder,s_id,wxapp_id,appid";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\DmhmodularService::update($data);
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
			\app\subschool\model\Dmhmodular::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function copy()
	{
		$modules = [["title" => "取快递", "image" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/modules/qu_icon.png", "url" => "/gc_school/pages/public/index?type=1"], ["title" => "寄快递", "image" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/modules/ji_icon.png", "url" => "/gc_school/pages/public/index?type=2"], ["title" => "食堂超市", "image" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/modules/shi_icon.png", "url" => "/gc_school/pages/canteen/canteen?type=1"], ["title" => "万能任务", "image" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/modules/wan_icon.png", "url" => "/gc_school/pages/public/index?type=4"]];
		foreach ($modules as &$v) {
			$insert["title"] = $v["title"];
			$insert["image"] = $v["image"];
			$insert["types"] = 0;
			$insert["start"] = 2;
			$insert["appid"] = $v["url"];
			$insert["ladder"] = 2;
			$insert["s_id"] = session("subschool.s_id");
			$insert["wxapp_id"] = session("subschool.wxapp_id");
			$insert["create_time"] = time();
			\think\facade\Db::name("dmh_modular")->insert($insert);
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function discount()
	{
		$wxapp_id = session("subschool.wxapp_id");
//		$url1 = "http://send.fkynet.net/api/Plugins/is_purchase";
        $url1 ="";
        $res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=5&user_wxappid=" . $wxapp_id);
		$res = json_decode($res, true);
		if ($res["code"] == 200) {
			$data = 1;
		} else {
			$data = 0;
		}
		return $data;
	}
}