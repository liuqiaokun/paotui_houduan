<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Setting extends Common
{
	public function index()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["wxapp_id"] = $this->request->get("wxapp_id", "", "serach_in");
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\SettingService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function view()
	{
		$data["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$res["sys"] = \think\facade\Db::name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
}