<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

class Index extends Base
{
	public function index()
	{
		$this->view->assign("media", \app\ApplicationName\service\BaseService::getMedia());
		$this->view->assign("pid", 0);
		$default_themes = config("xhadmin.default_themes") ? config("xhadmin.default_themes") : "index";
		return view($default_themes . "/index");
	}
}