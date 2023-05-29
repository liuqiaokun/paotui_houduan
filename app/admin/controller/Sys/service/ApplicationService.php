<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Sys\service;

class ApplicationService extends \xhadmin\CommonService
{
	public static function saveData($type, $data)
	{
		try {
			$rule = ["name" => "require", "app_dir" => ["regex" => "/^([a-z])+\$/"], "login_table" => ["regex" => "/^[a-z_]\\w+\$/"], "pk" => ["regex" => "/^([a-z_])+\$/"]];
			$msg = ["name.require" => "应用名称必填", "app_dir.require" => "应用目录必填", "login_table.regex" => "小写字母下划线数字组合", "pk.regex" => "主键小写字母下划线组合"];
			$validate = \think\facade\Validate::rule($rule)->message($msg);
			if (!$validate->check($data)) {
				throw new ValidateException($validate->getError());
			}
			if ($data["login_fields"] && !strpos($data["login_fields"], "|")) {
				throw new \Exception("登录字段格式错误");
			}
			if ($type == "add") {
				$reset = \app\admin\controller\Sys\model\Application::create($data);
				if ($reset->app_id && $data["app_type"] == 1) {
					self::createDefaultAction($reset->app_id);
				}
			} elseif ($type == "edit") {
				$reset = \app\admin\controller\Sys\model\Application::update($data);
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return $reset;
	}
	public static function createDefaultAction($app_id)
	{
		$applicationInfo = \app\admin\controller\Sys\model\Application::find($app_id);
		$res = \app\admin\controller\Sys\model\Menu::create(["title" => "系统管理", "is_create" => 0, "table_status" => 0, "is_url" => 0, "status" => 1, "menu_icon" => "fa fa-gears", "app_id" => $app_id]);
		if ($res->menu_id) {
			\app\admin\controller\Sys\model\Menu::update(["sortid" => $res->menu_id, "menu_id" => $res->menu_id]);
			$url = $applicationInfo["app_dir"] . "/Index/main";
			\app\admin\controller\Sys\model\Menu::create(["title" => "后台首页", "pid" => $res->menu_id, "is_create" => 0, "table_status" => 0, "is_url" => 1, "status" => 1, "sortid" => 1, "menu_icon" => "fa fa-gears", "app_id" => $app_id, "url" => $url]);
			$url = $applicationInfo["app_dir"] . "/Base/password";
			\app\admin\controller\Sys\model\Menu::create(["title" => "修改密码", "pid" => $res->menu_id, "is_create" => 0, "table_status" => 0, "is_url" => 1, "status" => 1, "sortid" => 2, "menu_icon" => "fa fa-gears", "app_id" => $app_id, "url" => $url]);
		}
	}
}