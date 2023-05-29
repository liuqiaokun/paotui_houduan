<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class Plugins extends Admin
{
	public function index()
	{
		if (!$this->request->isAjax()) {
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,name,identification";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\admin\service\PluginsService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
//			$url = "http://send.fkynet.net/api/Plugins/list";
            $url = "";
			$resu = file_get_contents($url . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1");
			$res["rows"] = json_decode($resu, true)["list"];
			$res["total"] = count(json_decode($resu, true)["list"]);
			return json($res);
		}
	}
	public function install_check()
	{
		$pid = $_POST["pid"];
		$type = $_POST["type"];
//		$url = "http://send.fkynet.net/api/Plugins/check";
        $url = "";
        $res = file_get_contents($url . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=" . $pid);
		$res = json_decode($res, true);
		if ($res["info"]["dead_date"] < time() && $res["status"] == 666) {
			return json(["status" => "01", "data" => "", "msg" => "该插件服务费已到期，请联系管理员续费"]);
		}
		if ($res["status"] == 666) {
			return json(["status" => "00", "data" => $res["url"], "msg" => "验证成功，正在更新。。。"]);
		} else {
			return json(["status" => "01", "data" => "", "msg" => "该域名未购买该插件"]);
		}
	}
	public function install()
	{
		$source_url = $this->request->post("url");
		$pid = $this->request->post("pid");
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
//				$url1 = "http://send.fkynet.net/api/Plugins/update_domain";
                $url1 = "";
				$res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=" . $pid);
				return json(["status" => "00", "msg" => "操作成功"]);
			} else {
				return json(["status" => "01", "msg" => "解压失败"]);
			}
		} else {
			return json(["status" => "01", "msg" => "安装包不存在"]);
		}
	}
}