<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class Index extends Admin
{
	public function index()
	{
		$menu = $this->getSubMenu(0);
		$cmsMenu = (include app()->getRootPath() . "/app/admin/controller/Cms/config.php");
		if ($cmsMenu) {
			$menu = array_merge($cmsMenu, $menu);
		}
		$this->view->assign("menus", $menu);
		return view("index");
	}
	private function getSubMenu($pid)
	{
		$list = db("menu")->where(["status" => 1, "app_id" => 1, "pid" => $pid])->order("sortid asc")->select();
		if ($list) {
			foreach ($list as $key => $val) {
				$sublist = db("menu")->where(["status" => 1, "app_id" => 1, "pid" => $val["menu_id"]])->order("sortid asc")->select();
				if ($sublist) {
					$menus[$key]["sub"] = $this->getSubMenu($val["menu_id"]);
				}
				$menus[$key]["title"] = $val["title"];
				$menus[$key]["icon"] = !empty($val["menu_icon"]) ? $val["menu_icon"] : "fa fa-clone";
				$menus[$key]["url"] = !empty($val["url"]) ? strpos($val["url"], "://") ? $val["url"] : url($val["url"]) : url("admin/" . str_replace("/", ".", $val["controller_name"]) . "/index");
				$menus[$key]["access_url"] = !empty($val["url"]) ? $val["url"] : "admin/" . str_replace("/", ".", $val["controller_name"]);
			}
			return $menus;
		}
	}
	public function main()
	{
		return view("main");
	}
}