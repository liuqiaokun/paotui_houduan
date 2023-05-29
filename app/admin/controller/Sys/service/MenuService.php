<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Sys\service;

class MenuService extends \xhadmin\CommonService
{
	public static function saveData($type, $data)
	{
		try {
			if (strtolower($data["controller_name"]) == "sys") {
				throw new \Exception("系统名称禁止使用");
			}
			$controller_name = $data["controller_name"];
			if (strpos($controller_name, "/") > 0) {
				$arr = explode("/", $controller_name);
				$controller_name = ucfirst($arr[0]) . "/" . ucfirst($arr[1]);
			} else {
				$controller_name = ucfirst($controller_name);
			}
			$data["controller_name"] = $controller_name;
			$data["table_name"] = strtolower($data["table_name"]);
			$data["pk_id"] = strtolower($data["pk_id"]);
			if ($type == "add") {
				$controllerInfo = \app\admin\controller\Sys\model\Menu::where(["app_id" => $data["app_id"], "controller_name" => $data["controller_name"]])->find();
				if ($data["controller_name"] && $controllerInfo) {
					throw new \Exception("控制器已存在");
				}
				$tableInfo = \app\admin\controller\Sys\model\Menu::where(["table_status" => 1, "table_name" => $data["table_name"]])->find();
				if ($data["table_status"] && $data["table_name"] && $tableInfo) {
					throw new \Exception("数据表已存在");
				}
				$reset = \app\admin\controller\Sys\model\Menu::create($data);
				if ($reset->menu_id) {
					\app\admin\controller\Sys\model\Menu::update(["menu_id" => $reset->menu_id, "sortid" => $reset->menu_id]);
					$applicationInfo = \app\admin\controller\Sys\model\Application::find($data["app_id"]);
					if ($data["table_name"] && $data["pk_id"] && $applicationInfo["app_type"] == 1) {
						self::createDefaultAction($data, $reset->menu_id);
					}
				}
			} elseif ($type == "edit") {
				$reset = \app\admin\controller\Sys\model\Menu::update($data);
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return $reset->menu_id;
	}
	public static function createDefaultAction($data, $id)
	{
		db("action")->insertGetId(["menu_id" => $id, "name" => "首页数据列表", "action_name" => "index", "type" => "1", "is_view" => "0", "sortid" => "1"]);
		db("action")->insertGetId(["menu_id" => $id, "name" => "修改排序开关按钮操作", "action_name" => "updateExt", "type" => "16", "is_view" => "0", "sortid" => "2"]);
		db("field")->insertGetId(["menu_id" => $id, "name" => "编号", "field" => $data["pk_id"], "type" => "1", "list_show" => "1", "search_show" => "0", "is_post" => "0", "is_field" => "1", "align" => "center", "sortid" => "1", "datatype" => "int", "length" => "11"]);
	}
	public static function arrowsort($id, $type)
	{
		$data = \app\admin\controller\Sys\model\Menu::find($id);
		if ($type == 1) {
			$map = "sortid < " . $data["sortid"] . " and pid = " . $data["pid"];
			$info = \app\admin\controller\Sys\model\Menu::where($map)->order("sortid desc")->find();
		} else {
			$map = "sortid > " . $data["sortid"] . " and pid = " . $data["pid"];
			$info = \app\admin\controller\Sys\model\Menu::where($map)->order("sortid asc")->find();
		}
		try {
			if ($info && $data) {
				\app\admin\controller\Sys\model\Menu::update(["menu_id" => $id, "sortid" => $info["sortid"]]);
				\app\admin\controller\Sys\model\Menu::update(["menu_id" => $info["menu_id"], "sortid" => $data["sortid"]]);
			} else {
				throw new \Exception("目标位置没有数据");
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return true;
	}
	public static function delete($id)
	{
		try {
			$reset = \app\admin\controller\Sys\model\Menu::destroy(["menu_id" => $id]);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return $reset;
	}
}