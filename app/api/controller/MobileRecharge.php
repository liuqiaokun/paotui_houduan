<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class MobileRecharge extends Common
{
	public function index()
	{
//		$url1 = "http://send.fkynet.net/api/Plugins/is_purchase";
        $url1 = "";
        $res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=3");
		$res = json_decode($res, true);
		if ($res["code"] != 200) {
			return $this->ajaxReturn($this->errorCode, $res["msg"], htmlOutList($res));
		}
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$where["s_id"] = $this->request->post("s_id", "", "serach_in");
		$field = "*";
		$orderby = "mid desc";
		$res = \app\api\service\MobileRechargeService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}