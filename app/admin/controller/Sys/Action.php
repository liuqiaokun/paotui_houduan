<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Sys;

class Action extends \app\admin\controller\Admin
{
	public function initialize()
	{
		parent::initialize();
		config(["view_path" => app_path()], "view");
	}
	private function getTableList($connect)
	{
		$list = \think\facade\Db::connect($connect)->query("show tables");
		foreach ($list as $k => $v) {
			$tableList[] = str_replace(config("database.connections." . $connect . ".prefix"), "", $v["Tables_in_" . config("database.connections." . $connect . ".database")]);
		}
		$no_show_table = ["application", "menu", "config", "role", "upload_config", "action", "access", "field", "log"];
		foreach ($tableList as $key => $val) {
			if (in_array($val, $no_show_table)) {
				unset($tableList[$key]);
			}
		}
		return $tableList;
	}
	public function index()
	{
		if (!$this->request->isAjax()) {
			$menu_id = $this->request->get("menu_id", "", "intval");
			$menuInfo = model\Menu::find($menu_id);
			$applicationInfo = model\Application::find($menuInfo["app_id"]);
			$tpl = $applicationInfo["app_type"] == 1 ? "controller/Sys/view/action/index" : "controller/Sys/view/action/api_index";
			$this->view->assign("menu_id", $menu_id);
			return view($tpl);
		} else {
			$limit = input("post.limit", 20, "intval");
			$offset = input("post.offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$menu_id = $this->request->get("menu_id", "", "intval");
			$limit = ($page - 1) * $limit . "," . $limit;
			try {
				$data["rows"] = model\Action::where(["menu_id" => $menu_id])->order("sortid asc")->select();
				$data["total"] = model\Action::where(["menu_id" => $menu_id])->count();
			} catch (\Exception $e) {
				exit($e->getMessage());
			}
			$list = $data["rows"];
			$menuInfo = model\Menu::find($menu_id);
			$applicationInfo = model\Application::find($menuInfo["app_id"]);
			if ($applicationInfo["app_type"] == 1) {
				$actionList = service\ActionSetService::actionList() + service\ExtendService::$adminActions;
			} else {
				$actionList = service\ActionSetService::apiList() + service\ExtendService::$apiActions;
			}
			foreach ($list as $key => $val) {
				$list[$key]["type"] = $actionList[$val["type"]];
			}
			$data["rows"] = $list;
			return json($data);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			$menu_id = $this->request->get("menu_id", "", "intval");
			$menuInfo = model\Menu::find($menu_id);
			$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
			$applicationInfo = model\Application::find($menuInfo["app_id"]);
			$tpl = $applicationInfo["app_type"] == 1 ? "controller/Sys/view/action/info" : "controller/Sys/view/action/api_info";
			$actionList = $applicationInfo["app_type"] == 1 ? service\ActionSetService::actionList() : service\ActionSetService::apiList();
			$extendAction = $applicationInfo["app_type"] == 1 ? service\ExtendService::$adminActions : service\ExtendService::$apiActions;
			$this->view->assign("menu_id", $menu_id);
			$this->view->assign("actionList", $actionList + $extendAction);
			$this->view->assign("requestList", service\ActionSetService::requestList());
			$this->view->assign("tableList", $this->getTableList($connect));
			return view($tpl);
		} else {
			$data = $this->request->post();
			$data["sql_query"] = $this->request->post("sql_query", "", "sql_replace");
			try {
				service\ActionService::saveData("add", $data);
			} catch (\Exception $e) {
				$this->error($e->getMessage());
			}
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function fast()
	{
		if (!$this->request->isPost()) {
			$this->view->assign("menu_id", $this->request->param("menu_id"));
			return view("controller/Sys/view/action/fast");
		} else {
			$data = $this->request->post();
			try {
				service\ActionService::addFast($data);
			} catch (\Exception $e) {
				$this->error($e->getMessage());
			}
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$id = $this->request->get("id", "", "intval");
			if (!$id) {
				$this->error("参数错误");
			}
			$actionInfo = model\Action::find($id);
			$menuInfo = model\Menu::find($actionInfo["menu_id"]);
			$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
			$applicationInfo = model\Application::find($menuInfo["app_id"]);
			$tpl = $applicationInfo["app_type"] == 1 ? "controller/Sys/view/action/info" : "controller/Sys/view/action/api_info";
			$actionList = $applicationInfo["app_type"] == 1 ? service\ActionSetService::actionList() : service\ActionSetService::apiList();
			$extendAction = $applicationInfo["app_type"] == 1 ? service\ExtendService::$adminActions : service\ExtendService::$apiActions;
			$this->view->assign("actionList", $actionList + $extendAction);
			$this->view->assign("info", $actionInfo);
			$this->view->assign("menu_id", $actionInfo["menu_id"]);
			$this->view->assign("requestList", service\ActionSetService::requestList());
			$this->view->assign("tableList", $this->getTableList($connect));
			return view($tpl);
		} else {
			$data = $this->request->post();
			$data["sql_query"] = $this->request->post("sql_query", "", "sql_replace");
			try {
				service\ActionService::saveData("edit", $data);
			} catch (\Exception $e) {
				$this->error($e->getMessage());
			}
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function setSort()
	{
		$id = $this->request->post("id", "", "intval");
		$sortid = $this->request->post("sortid", "", "intval");
		if (!$id || !$sortid) {
			$this->error("参数错误");
		}
		try {
			model\Action::update(["id" => $id, "sortid" => $sortid]);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		return json(["status" => "00", "msg" => "修改成功"]);
	}
	public function arrowsort()
	{
		$id = $this->request->post("id", "", "intval");
		$type = $this->request->post("type", "", "intval");
		if (!$id || !$type) {
			$this->error("参数错误");
		}
		try {
			service\ActionService::arrowsort($id, $type);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		return json(["status" => "00", "msg" => "设置成功"]);
	}
	public function delete()
	{
		$id = $this->request->post("id", "", "intval");
		if (!$id) {
			$this->error("参数错误");
		}
		$info = db("action")->where("id", $id)->find();
		try {
			$res = model\Action::destroy($id);
			if ($res && $info["type"] == 31) {
				$delete_field = !is_null(config("my.delete_field")) ? config("my.delete_field") : "delete_time";
				$menuInfo = db("menu")->where("menu_id", $info["menu_id"])->find();
				$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
				if ($this->getFieldStatus(config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"], $delete_field, $connect)) {
					$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"] . " DROP " . $delete_field;
					\think\facade\Db::connect($connect)->execute($sql);
				}
			}
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		return json(["status" => "00", "msg" => "删除成功"]);
	}
	public function updateExt()
	{
		$data = $this->request->post();
		if (!$data["id"]) {
			$this->error("参数错误");
		}
		try {
			model\Action::update($data);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function config()
	{
		return view("controller/Sys/view/action/config");
	}
	public function getFieldStatus($tablename, $field, $connect)
	{
		$list = \think\facade\Db::connect($connect)->query("show columns from " . $tablename);
		foreach ($list as $v) {
			$arr[] = $v["Field"];
		}
		if (in_array($field, $arr)) {
			return true;
		}
	}
}