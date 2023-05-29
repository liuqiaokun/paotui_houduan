<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

class Base extends \app\BaseController
{
	protected $request;
	protected $app;
	protected $view;
	public function __construct(\think\App $app, \think\View $view)
	{
		$this->app = $app;
		$this->view = $view;
		$this->request = $this->app->request;
		$this->initConfig();
	}
	public function initConfig()
	{
		$list = db("config")->cache(true, 60)->select()->column("data", "name");
		config($list, "xhadmin");
		if ($this->request->isMobile() && config("xhadmin.mobile_status")) {
			$parse_url = parse_url($this->request->url(true));
			if (config("xhadmin.mobile_domain") && config("xhadmin.mobile_domain") != $parse_url["host"]) {
				header("location:http://" . config("xhadmin.mobile_domain") . $parse_url["path"]);
				exit;
			}
			$list["default_themes"] = config("xhadmin.mobile_themes");
			config($list, "xhadmin");
		}
	}
	public function __call($method, $args)
	{
		throw new \think\exception\FuncNotFoundException("方法不存在", $method);
	}
}