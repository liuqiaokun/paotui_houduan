<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Admin extends \app\BaseController
{
	public function initialize()
	{
		error_reporting(0);
		function getTopDomainhuo()
		{
			$host = $_SERVER["HTTP_HOST"];
			$matchstr = "[^\\.]+\\.(?:(" . $str . ")|\\w{2}|((" . $str . ")\\.\\w{2}))\$";
			if (preg_match("/" . $matchstr . "/ies", $host, $matchs)) {
				$domain = $matchs[0];
			} else {
				$domain = $host;
			}
			return $domain;
		}
		$domain = getTopDomainhuo();
		/* $real_domain = "baidu.com";
		$check_host = "http://key.gechuang.net/update.php";
		$client_check = $check_host . "?a=client_check&u=" . $_SERVER["HTTP_HOST"];
		$check_message = $check_host . "?a=check_message&u=" . $_SERVER["HTTP_HOST"];
		$check_info = file_get_contents($client_check);
		$message = file_get_contents($check_message);
		if ($check_info == "1") {
			echo "<font color=red>" . $message . "</font>";
			exit;
		} elseif ($check_info == "2") {
			echo "<font color=red>" . $message . "</font>";
			exit;
		} elseif ($check_info == "3") {
			echo "<font color=red>" . $message . "</font>";
			exit;
		}
		if ($check_info !== "0") {
			if ($domain !== $real_domain) {
				echo "远程检查失败了。请联系授权提供商。";
				exit;
			}
		} */
		unset($domain);
		$controller = $this->request->controller();
		$action = $this->request->action();
		$app = app("http")->getName();
		$accounts = session("accounts");
		$userid = session("accounts_sign") == data_auth_sign($accounts) ? true : false;
		if (!$userid && ($app != "accounts" || $controller != "Login")) {
			echo "<script type=\"text/javascript\">top.parent.frames.location.href=\"" . url("accounts/Login/index") . "\";</script>";
			exit;
		}
		foreach (config("my.nocheck") as $val) {
			$nocheck[] = str_replace("admin", "accounts", $val);
		}
		$url = "{$app}/{$controller}/{$action}";
		if (session("accounts.role_id") != 1 && !in_array($url, $nocheck) && $action !== "startImport") {
			if (!in_array($url, session("accounts.nodes"))) {
				throw new \think\exception\ValidateException("你没操作权限");
			}
		}
		event("DoLog");
		$list = db("config")->cache(true, 60)->select()->column("data", "name");
		config($list, "xhadmin");
	}
}