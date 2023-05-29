<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class Version extends Admin
{
	public function index()
	{
		$config = \think\facade\Db::name("config")->column("data", "name");
//		$url1 = "http://send.fkynet.net/api/Version/index";
        $url1 = "";

        $res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1");
		$res = json_decode($res, true);
		$res["content"] = htmlspecialchars_decode($res["content"]);
		$this->view->assign("data", $res);
		$this->view->assign("config", $config);
		return view("index");
	}
	public function test()
	{
//		$url1 = "http://send.fkynet.net/api/Version/check";
        $url1 = "";

        $res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1");
		$res = json_decode($res, true);
		if ($res["info"]["enddate"] < time() && $res["status"] == 666) {
			return json(["status" => "01", "data" => "", "msg" => "该域名服务费已到期，请联系管理员续费"]);
		}
		if ($res["status"] == 666) {
			return json(["status" => "00", "data" => $res["url"], "msg" => "验证成功，正在更新。。。"]);
		} else {
			return json(["status" => "01", "data" => "", "msg" => "该域名为盗版"]);
		}
	}
	public function down()
	{
		$source_url = $this->request->post("url");
		define("ROOT_PATH", str_replace("\\", "/", dirname(__FILE__)) . "/");
		$path = str_replace("public", "", $_SERVER["DOCUMENT_ROOT"]);
		file_put_contents($path . "app.zip", file_get_contents($source_url));
		$tmpfile = $path . "app.zip";
		if (file_exists($tmpfile)) {
			$zip = new \ZipArchive();
			$res = $zip->open($tmpfile, \ZipArchive::CREATE);
			if ($res === true) {
				$zip->extractTo($path);
				$zip->close();
				include $path . "/app/database.php";
				unlink($path . "/app/database.php");
//				$url1 = "http://send.fkynet.net/api/Version/index";
                $url1 = "";

                $res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1");
				$res = json_decode($res, true);
				\think\facade\Db::name("config")->where(["name" => "version"])->update(["data" => $res["version"]]);
				return json(["status" => "00", "msg" => "升级成功"]);
			} else {
				return json(["status" => "01", "msg" => "解压失败"]);
			}
		} else {
			return json(["status" => "01", "msg" => "安装包不存在"]);
		}
	}
}