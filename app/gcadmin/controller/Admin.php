<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller;

class Admin extends \app\BaseController
{
	public function initialize()
	{
		$controller = $this->request->controller();
		$action = $this->request->action();
		$app = app("http")->getName();
		$admin = session("gcadmin");
		$userid = session("gcadmin_sign") == data_auth_sign($admin) ? $admin["user_id"] : 0;
		if (!$userid && ($app != "gcadmin" || $controller != "Login")) {
			echo "<script type=\"text/javascript\">top.parent.frames.location.href=\"" . url("gcadmin/Login/index") . "\";</script>";
			exit;
		}
		if (session("gcadmin.nodes")) {
			foreach (session("gcadmin.nodes") as $key => $val) {
				$newnodes[] = parse_url($val)["path"];
			}
		}
		$url = "{$app}/{$controller}/{$action}";
		if (session("gcadmin.role_id") != 1 && !in_array($url, config("my.nocheck")) && $action !== "startImport" && $action !== "getExtends") {
			if (!in_array($url, $newnodes)) {
				throw new \think\exception\ValidateException("你没操作权限");
			}
		}
		event("DoLog");
		$list = db("config")->cache(true, 60)->column("data", "name");
		config($list, "xhadmin");
	}
	public function __call($method, $args)
	{
		throw new \think\exception\FuncNotFoundException("方法不存在", $method);
	}
}