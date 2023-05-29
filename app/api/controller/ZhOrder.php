<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhOrder extends Common
{
	public function getNewestOrderList()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		$limit = $this->request->get("limit", 10, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["status"] = 2;
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\ZhOrderService::getNewestOrderLstList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as &$v) {
			$v = \app\api\service\OrderService::statusWhere($v);
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function getHistoryOrderList()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$limit = $this->request->get("limit", 10, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["status"] = 4;
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\ZhOrderService::getHistoryOrderListList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as &$v) {
			$v = \app\api\service\OrderService::statusWhere($v);
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}