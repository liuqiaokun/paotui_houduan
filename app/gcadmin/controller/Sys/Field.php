<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys;

class Field extends \app\gcadmin\controller\Admin
{
	public function initialize()
	{
		parent::initialize();
		config(["view_path" => app_path()], "view");
	}
	private function getTpl($app_id, $extend)
	{
		$applicationInfo = model\Application::find($app_id);
		switch ($applicationInfo["app_type"]) {
			case 1:
				$tpl = "controller/Sys/view/field/admin_" . $extend;
				break;
			case 2:
				$tpl = "controller/Sys/view/field/api_" . $extend;
				break;
			case 3:
				$tpl = "controller/Sys/view/field/cms_" . $extend;
				break;
		}
		return $tpl;
	}
	public function index()
	{
		if (!$this->request->isAjax()) {
			$menu_id = $this->request->get("menu_id", "", "intval");
			if ($menu_id == config("my.config_module_id")) {
				$tpl = "controller/Sys/view/field/config_index";
			} else {
				$tpl = $this->getTpl(model\Menu::find($menu_id)["app_id"], "index");
			}
			$this->view->assign("menu_id", $menu_id);
			return view($tpl);
		} else {
			$limit = input("post.limit", 20, "intval");
			$offset = input("post.offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$menu_id = $this->request->get("menu_id", "", "intval");
			try {
				$res = model\Field::where(["menu_id" => $menu_id])->order("sortid asc")->paginate(["list_rows" => $limit, "page" => $page]);
			} catch (\Exception $e) {
				exit($e->getMessage());
			}
			$list = $res->items();
			foreach ($list as $key => $val) {
				$typeField = service\FieldSetService::typeField() + service\ExtendService::$fields;
				$fieldInfo = $typeField[$val["type"]];
				$list[$key]["type"] = $fieldInfo["name"];
				if (!empty($val["datatype"])) {
					$list[$key]["datatype"] = $val["datatype"] !== "decimal" ? $val["datatype"] . "(" . $val["length"] . ")" : $val["datatype"] . "(" . $val["length"] . ",2)";
				}
			}
			$data["total"] = $res->total();
			$data["rows"] = $list;
			return json($data);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			$menu_id = $this->request->get("menu_id", "", "intval");
			$this->view->assign("fieldList", service\FieldSetService::typeField() + service\ExtendService::$fields);
			$this->view->assign("tabList", service\FieldSetService::tabList($menu_id));
			$this->view->assign("ruleList", service\FieldSetService::ruleList());
			$this->view->assign("dateList", service\FieldSetService::dateList());
			$this->view->assign("propertyList", service\FieldSetService::propertyField());
			if ($menu_id == config("my.config_module_id")) {
				$tpl = "controller/Sys/view/field/config_info";
			} else {
				$tpl = $this->getTpl(model\Menu::find($menu_id)["app_id"], "info");
			}
			$this->view->assign("menu_id", $menu_id);
			return view($tpl);
		} else {
			$data = $this->request->post();
			$data["sql"] = $this->request->post("sql", "", "sql_replace");
			try {
				service\FieldService::saveData("add", $data);
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
			$info = checkData(model\Field::find($id));
			$menuInfo = model\Menu::find($info["menu_id"]);
			$this->view->assign("fieldList", service\FieldSetService::typeField() + service\ExtendService::$fields);
			$this->view->assign("tabList", service\FieldSetService::tabList($info["menu_id"]));
			$this->view->assign("ruleList", service\FieldSetService::ruleList());
			$this->view->assign("dateList", service\FieldSetService::dateList());
			$this->view->assign("propertyList", service\FieldSetService::propertyField());
			$this->view->assign("info", $info);
			if ($menuInfo["menu_id"] == config("my.config_module_id")) {
				$tpl = "controller/Sys/view/field/config_info";
			} else {
				$applicationInfo = model\Application::find($menuInfo["app_id"]);
				$tpl = $this->getTpl($menuInfo["app_id"], "info");
			}
			$this->view->assign("menu_id", $info["menu_id"]);
			return view($tpl);
		} else {
			$data = $this->request->post();
			$data["sql"] = $this->request->post("sql", "", "sql_replace");
			try {
				service\FieldService::saveData("edit", $data);
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
			model\Field::update(["id" => $id, "sortid" => $sortid]);
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
			service\FieldService::arrowsort($id, $type);
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
		try {
			model\Field::destroy($id);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		return json(["status" => "00", "msg" => "删除成功"]);
	}
	public function getFieldConfig()
	{
		$type = $this->request->post("type", "", "intval");
		$typeField = service\FieldSetService::typeField();
		$propertyField = service\FieldSetService::propertyField();
		$typeData = $typeField[$type];
		$property = $propertyField[$typeData["property"]];
		return json($property);
	}
	public function updateExt()
	{
		$data = $this->request->post();
		if (!$data["id"]) {
			$this->error("参数错误");
		}
		try {
			model\Field::update($data);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function getPy()
	{
		$fieldname = $this->request->post("fieldname");
		$filed_name_status = !is_null(config("my.filed_name_status")) ? config("my.filed_name_status") : false;
		if ($filed_name_status) {
			$fieldname = preg_replace("/\\s+/", "_", $fieldname);
			$fieldname = substr(\org\Pinyin::output($fieldname, true), 0, 30);
			return json(["status" => "00", "fieldname" => $fieldname]);
		}
	}
}