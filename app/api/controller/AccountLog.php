<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class AccountLog extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$date = $this->request->post("date");
		$type = $this->request->post("type", 1);
		$start = $date . "-01 00:00:00";
		$end = date("Y-m-d", strtotime("+1month -1day", strtotime($date))) . " 23:59:59";
		$where = [];
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$where["uid"] = $this->request->uid;
		$operate = $this->request->post("operate");
		if (!is_null($operate)) {
			$where["operate"] = $operate;
		}
		if ($type == 3) {
			$where["type"] = 3;
		} else {
			$where["type"] = ["in", [0, 1, 2]];
		}
		$where["addtime"] = ["between", [$start, $end]];
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\AccountLogService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function bean()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$date = $this->request->post("date");
		$type = $this->request->post("type");
		$start = $date . "-01 00:00:00";
		$end = date("Y-m-d", strtotime("+1month -1day", strtotime($date))) . " 23:59:59";
		$uid = $this->request->uid;
		$where = [];
		$store = \think\facade\Db::name("zh_business")->where("wxadmin_name", $uid)->find();
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$where["bus_id"] = $store["business_id"];
		if ($type >= 0) {
			$where["type"] = $type;
		}
		$orderby = "id desc";
		$res = \think\facade\Db::name("business_account_log")->where($where)->where("addtime", "between", [$start, $end])->order($orderby)->page($page, $limit)->select();
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
}