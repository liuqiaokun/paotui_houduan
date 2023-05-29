<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Execute extends Common
{
	public function query()
	{
		$version = \think\facade\Db::name("config")->where("name", "version")->find();
		if (empty($version)) {
			\think\facade\Db::name("config")->insert(["name" => "version", "data" => "1.0.0"]);
		}
		$menu = \think\facade\Db::name("menu")->where("title", "版本管理")->find();
		if (empty($menu)) {
			\think\facade\Db::name("menu")->insert(["controller_name" => "Version", "title" => "版本管理", "status" => 1, "app_id" => 1]);
		}
	}
}