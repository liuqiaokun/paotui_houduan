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
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\ReserveService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$postField = "order_no,code,date,uid,time,status,createtime,num,t_id,list";
		$data = $this->request->only(explode(",", $postField), "post", null);
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
		$data["order_no"] = "AT" . time() . rand(9, 100) . rand(9, 100);
		$data["uid"] = $this->request->uid;
		$res = \app\api\service\ReserveService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function view()
	{
		$data["uid"] = $this->request->uid;
		$data["id"] = $this->request->post("id", "", "serach_in");
		$field = "id,order_no,code,date,uid,time,status,createtime,num,t_id,list";
		$res = checkData(\app\api\model\Reserve::field($field)->where($data)->find());
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function ji()
	{
		echo "测试不被覆盖";
	}
}