<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhfootprint extends Common
{
	public function index()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["a.u_id"] = $this->request->uid;
		$where["a.s_id"] = $this->request->post("s_id", "", "serach_in");
		$where["a.wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$field = "a.m_id,b.title,b.image,b.create_time,b.pay";
		$orderby = "id desc";
		$res = \app\api\service\DmhfootprintService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		$list = [];
		foreach ($res["list"] as $key => $v) {
			$list[$key] = $v;
			$list[$key]["create_time"] = date("Y-m-d h:i", $v["create_time"]);
		}
		$res["list"] = $list;
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function delete()
	{
		$idx = $this->request->post("ids", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["id"] = explode(",", $idx);
		try {
			\app\api\model\Dmhfootprint::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function add()
	{
		$postField = "u_id,s_id,m_id,wxapp_id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$user = $this->app->db->name("dmh_footprint")->where($data)->find();
		if ($user) {
			return $this->ajaxReturn($this->successCode, "您已浏览过该商品");
		}
		$data["create_time"] = time();
		$res = \app\api\service\DmhfootprintService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
}