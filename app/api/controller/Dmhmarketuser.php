<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhmarketuser extends Common
{
	public function collection()
	{
		$postField = "u_id,s_id,wxapp_id";
		$where = $this->request->only(explode(",", $postField), "post", null);
		$where["u_id"] = $this->request->uid;
		$stay = $this->app->db->name("dmh_goods_stay")->where($where)->count();
		$footprint = $this->app->db->name("dmh_footprint")->where($where)->count();
		$res = ["stay" => $stay, "footprint" => $footprint];
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function stay()
	{
		$where["u_id"] = $this->request->uid;
		$stay = $this->app->db->name("dmh_goods_stay")->where($where)->paginate(10);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($stay));
	}
	public function goods()
	{
		$where["u_id"] = $this->request->uid;
		$where["s_id"] = input("s_id", "", "trim");
		$examine = input("examine", "", "trim");
		if ($examine == 1) {
			$where["examine"] = 0;
		}
		$goods = $this->app->db->name("dmh_market_goods")->where($where)->order("m_id desc")->paginate(10);
		$list = [];
		foreach ($goods as $k => $v) {
			$wheres = ["m_id" => $v["m_id"]];
			$list[$k] = $v;
			$list[$k]["con"] = $this->app->db->name("dmh_goods_stay")->where($wheres)->count();
			$stay = $this->app->db->name("dmh_goods_stay")->where("m_id", $v["m_id"])->where("u_id", $where["u_id"])->find();
			if ($stay) {
				$list[$k]["stay"] = 1;
			} else {
				$list[$k]["stay"] = 0;
			}
		}
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($list));
	}
	public function delorder()
	{
		$where["u_id"] = $this->request->uid;
		$where["s_id"] = input("s_id", "", "trim");
		$where["m_id"] = input("m_id", "", "trim");
		$goods = $this->app->db->name("dmh_market_goods")->where($where)->find();
		$state = $goods["state"] == 1 ? 0 : 1;
		$res = $this->app->db->name("dmh_market_goods")->where($where)->update(["state" => $state]);
		if ($res) {
			return $this->ajaxReturn($this->successCode, "修改成功");
		} else {
			return $this->ajaxReturn($this->errorCode, "修改失败");
		}
	}
	public function examine()
	{
		$data["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$examine = $this->app->db->name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
		if ($examine["second_check_switch"] == 1) {
			$datas["examine"] = 1;
		} else {
			$datas["examine"] = 0;
		}
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($datas));
	}
}