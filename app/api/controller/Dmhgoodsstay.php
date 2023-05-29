<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhgoodsstay extends Common
{
	public function add()
	{
		$postField = "u_id,m_id,s_id,wxapp_id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$data["create_time"] = time();
		$res = \app\api\service\DmhgoodsstayService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function delete()
	{
		$idx = $this->request->post("m_id", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["u_id"] = $this->request->uid;
		$data["s_id"] = $this->request->post("s_id", "", "serach_in");
		$data["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		if ($this->request->post("type", "", "serach_in") == 1) {
			$data["m_id"] = $idx;
		} else {
			$data["id"] = $idx;
		}
		try {
			\app\api\model\Dmhgoodsstay::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function index()
	{
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$uid = $this->request->uid;
		$s_id = $where["s_id"] = $this->request->post("s_id", "", "serach_in");
		$wxapp_id = $where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$field = "a.id,a.m_id,b.title,b.image,b.create_time,b.pay,b.pay,b.price,b.u_id,b.degree";
		$orderby = "id desc";
		$res = \app\api\service\DmhgoodsstayService::indexList(formatWhere($where), $field, $orderby, $limit, $page, $uid, $s_id, $wxapp_id);
		$list = [];
		foreach ($res["list"] as $key => $v) {
			$list[$key] = $v;
			$wheres = ["m_id" => $v["m_id"]];
			$list[$key]["user"] = $this->app->db->name("wechat_user")->where("u_id", $v["u_id"])->find();
			$list[$key]["con"] = $this->app->db->name("dmh_goods_stay")->where($wheres)->count();
		}
		$res["list"] = $list;
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function view()
	{
		$data["u_id"] = $this->request->uid;
		$data["m_id"] = $this->request->post("m_id", "", "serach_in");
		$data["s_id"] = $this->request->post("s_id", "", "serach_in");
		$data["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$field = "id,m_id,u_id,create_time,s_id,wxapp_id";
		$res = $this->app->db->name("dmh_goods_stay")->where($data)->find();
		$res["is_fav"] = $res ? 1 : 0;
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
}