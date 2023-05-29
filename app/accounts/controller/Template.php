<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Template extends Admin
{
	public function index()
	{
		$wxapp_id = session("accounts.wxapp_id");
		$info = \app\accounts\model\Template::where("wxapp_id", $wxapp_id)->find();
		$this->view->assign("info", $info);
		return view("index");
	}
	public function update()
	{
		$type = $this->request->post("type");
		$wxapp_id = session("accounts.wxapp_id");
		$data["wxapp_id"] = $wxapp_id;
		$data["type"] = $type;
		$info = \app\accounts\model\Template::where("wxapp_id", $wxapp_id)->find();
		if ($info) {
			$data["id"] = $info["id"];
			$res = \app\accounts\service\TemplateService::update($data);
		} else {
			$res = \app\accounts\service\TemplateService::add($data);
		}
		if ($res) {
			return json(["status" => "00", "msg" => "操作成功"]);
		} else {
			return json(["status" => "01", "msg" => "操作失败"]);
		}
	}
}