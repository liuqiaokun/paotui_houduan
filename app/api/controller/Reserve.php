<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Reserve extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["uid"] = $this->request->uid;
		$where["status"] = $this->request->post("status", "", "serach_in");
		$dataList = \think\facade\Db::name("reserve")->where("status", 1)->where("uid", $this->request->uid)->select();
		foreach ($dataList as &$v) {
			$time = explode(" - ", $v["time"]);
			if ($v["date"] < date("Y-m-d") || $v["date"] == date("Y-m-d") && date("H:i:s") > $time[1]) {
				\think\facade\Db::name("reserve")->update(["id" => $v["id"], "status" => 2]);
			}
		}
		$field = "*";
		$orderby = "id desc";
		$ticket = \think\facade\Db::name("ticket")->field("t_id,type")->select();
		$ticket_list = [];
		foreach ($ticket as &$v) {
			$ticket_list[$v["t_id"]] = $v["title"];
		}
		$res = \app\api\service\ReserveService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as &$v) {
			$v["ticket_name"] = $ticket_list[$v["t_id"]];
		}
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$postField = "order_no,code,date,uid,time,status,createtime,num,t_id,list";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$reserve = \think\facade\Db::name("reserve")->where("date", $data["date"])->where("uid", $this->request->uid)->select();
		if (count($reserve) > 0) {
			return $this->ajaxReturn($this->errorCode, "今日已预约，不可再预约");
		}
		if ($data["t_id"] == "undefined" || $data["time"] == "undefined") {
			return $this->ajaxReturn($this->errorCode, "请先选择门票");
		}
		if (!($ticket = \think\facade\Db::name("ticket")->find($data["t_id"]))) {
			return $this->ajaxReturn($this->errorCode, "该门票不存在");
		}
		$with = explode(";", $data["list"]);
		if (count($with) > 4) {
			return $this->ajaxReturn($this->errorCode, "最多可预约四人");
		}
		foreach ($with as &$v) {
			if (!$v) {
				return $this->ajaxReturn($this->errorCode, "请填写用户信息");
			}
		}
		if (count(explode(";", $data["list"])) != $data["num"]) {
			return $this->ajaxReturn($this->errorCode, "用户信息与购买数量不匹配");
		}
		$where[] = ["date", "=", $data["date"]];
		$where[] = ["t_id", "=", $data["t_id"]];
		$order_count = \think\facade\Db::name("reserve")->where($where)->sum("num");
		$ticket = \think\facade\Db::name("ticket")->find($data["t_id"]);
		if ($ticket["type"] == 2) {
			$left = $ticket["stock"] - $order_count;
			if ($left < $data["num"]) {
				return $this->ajaxReturn($this->errorCode, "余票不足，请刷新重试");
			}
		}
		$data["uid"] = $this->request->uid;
		$repeat_where[] = ["date", "=", $data["date"]];
		$repeat_where[] = ["time", "=", $data["time"]];
		$repeat_where[] = ["num", "=", $data["num"]];
		$repeat_where[] = ["t_id", "=", $data["t_id"]];
		$repeat_where[] = ["list", "=", $data["list"]];
		$repeat_where[] = ["uid", "=", $data["uid"]];
		$repeat = \think\facade\Db::name("reserve")->where($repeat_where)->find();
		if (!empty($repeat)) {
			return $this->ajaxReturn($this->errorCode, "请勿重复提交");
		}
		$data["order_no"] = "AT" . time() . rand(9, 100) . rand(9, 100);
		$res = \app\api\service\ReserveService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function view()
	{
		$data["uid"] = $this->request->uid;
		$data["id"] = $this->request->post("id", "", "serach_in");
		$order = \think\facade\Db::name("reserve")->find($data["id"]);
		$time = explode(" - ", $order["time"]);
		if ($order["date"] < date("Y-m-d") || $order["date"] == date("Y-m-d") && date("H:i:s") > $time[1]) {
			\think\facade\Db::name("reserve")->update(["id" => $data["id"], "status" => 2]);
		}
		$field = "id,order_no,code,date,uid,time,status,createtime,num,t_id,list";
		$res = checkData(\app\api\model\Reserve::field($field)->where($data)->find());
		$ticket_info = \think\facade\Db::name("ticket")->where("t_id", $order["t_id"])->field("title,type")->find();
		$res["ticket_name"] = $ticket_info["title"];
		$res["ticket_type"] = $ticket_info["type"];
		$res["list"] = explode(";", $res["list"]);
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function delete()
	{
		$idx = $this->request->post("ids", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["uid"] = $this->request->uid;
		$data["id"] = explode(",", $idx);
		try {
			\app\api\model\Reserve::destroy($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function ji()
	{
		echo "测试不被覆盖";
	}
}