<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhexpress extends Common
{
	public function index()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["s_id"] = $this->request->get("s_id", "", "serach_in");
		$where["wxapp_id"] = $this->request->get("wxapp_id", "", "serach_in");
		$field = "s_id,wxapp_id,title,address";
		$orderby = "id desc";
		$res = \app\api\service\DmhexpressService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}