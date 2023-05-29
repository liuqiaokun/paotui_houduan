<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhmarketgoods extends Common
{
	public function add()
	{
		$postField = "u_id,cid,create_time,details,price,pay,s_id,wxapp_id,image,title,m_id,state";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$data["title"] = $this->request->post("name", "", "serach_in");
		$data["phone"] = $this->request->post("phone", "", "serach_in");
		$data["stock"] = $this->request->post("stock", "", "serach_in");
		$data["price"] = $this->request->post("oldprice", "", "serach_in");
		$data["pay"] = $this->request->post("newprice", "", "serach_in");
		$data["details"] = $this->request->post("content", "", "serach_in");
		$data["degree"] = $this->request->post("degree", "", "serach_in");
		$data["cid"] = $this->request->post("cate_id", "", "serach_in");
		$data["rotation"] = $this->request->post("img", "", "serach_in");
		$data["image"] = $this->request->post("image", "", "serach_in");
		$rotation = html_entity_decode($data["rotation"]);
		$examine = $this->app->db->name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
		if ($examine["second_check_switch"] == 1) {
			$data["examine"] = 0;
		} else {
			$data["examine"] = 1;
		}
		$res = \app\api\service\DmhmarketgoodsService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function update()
	{
		$postField = "m_id,u_id,cid,create_time,details,price,pay,s_id,wxapp_id,image,title,m_id,state";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (empty($data["m_id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["m_id"] = $data["m_id"];
		$data["u_id"] = $this->request->uid;
		$data["title"] = $this->request->post("name", "", "serach_in");
		$data["phone"] = $this->request->post("phone", "", "serach_in");
		$data["stock"] = $this->request->post("stock", "", "serach_in");
		$data["price"] = $this->request->post("oldprice", "", "serach_in");
		$data["pay"] = $this->request->post("newprice", "", "serach_in");
		$data["details"] = $this->request->post("content", "", "serach_in");
		$data["degree"] = $this->request->post("degree", "", "serach_in");
		$data["cid"] = $this->request->post("cate_id", "", "serach_in");
		$data["rotation"] = $this->request->post("img", "", "serach_in");
		$data["image"] = $this->request->post("image", "", "serach_in");
		$examine = $this->app->db->name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
		if ($examine["second_check_switch"] == 1) {
			$data["examine"] = 0;
		} else {
			$data["examine"] = 1;
		}
		$user = $this->app->db->name("dmh_market_goods")->where(["m_id" => $data["m_id"], "u_id" => $data["u_id"]])->find();
		if (empty($user)) {
			$this->errorCode("该商品不是您上传的商品");
		}
		$res = \app\api\service\DmhmarketgoodsService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function delete()
	{
		$idx = $this->request->post("m_ids", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["m_id"] = explode(",", $idx);
		try {
			\app\api\model\Dmhmarketgoods::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function view()
	{
		$data["m_id"] = $this->request->post("m_id", "", "trim");
		$res = checkData(\app\api\model\Dmhmarketgoods::find($data["m_id"]));
		$res["create_time"] = date("Y-m-d h:i", $res["create_time"]);
		$res["rotation"] = explode(",", $res["rotation"]);
		$res["user"] = $this->app->db->name("wechat_user")->where("u_id", $res["u_id"])->find();
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 10, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["a.s_id"] = $this->request->post("s_id", "", "serach_in");
		$uid = $this->request->post("uid", "", "serach_in");
		$where["state"] = 0;
		$where["examine"] = 1;
		if (!!input("cid", "", "trim")) {
			$where["cid"] = $this->request->post("cid", "", "serach_in");
		}
		if (!!input("degree", "", "trim")) {
			$where["degree"] = $this->request->post("degree", "", "serach_in");
		}
		$keywords = $this->request->post("keywords", "", "serach_in");
		$where["stock"] = [">", 0];
		$orderby = "m_id desc";
		if (input("sotrvalue", "", "trim") == "最新") {
			$orderby = "a.m_id desc";
		}
		if (input("sotrvalue", "", "trim") == "最热") {
			$field = "COUNT(c.m_id) as con, a.*";
		}
		if (input("priceFlag", "", "trim") == "false") {
			$orderby = "a.pay desc";
		}
		if (input("priceFlag", "", "trim") == "true") {
			$orderby = "a.pay asc";
		}
		$sotrvalue = input("sotrvalue", "", "trim");
		$res = \app\api\service\DmhmarketgoodsService::indexList(formatWhere($where), $field, $orderby, $limit, $page, $keywords, $sotrvalue);
		$list = [];
		foreach ($res["list"] as $key => $v) {
			$list[$key] = $v;
			$wheres = ["m_id" => $v["m_id"]];
			if ($sotrvalue == "最热") {
				$user = $this->app->db->name("wechat_user")->where("u_id", $v["u_id"])->find();
				$list[$key]["nickname"] = $user["nickname"];
				$list[$key]["avatar"] = $user["avatar"];
			} else {
				$list[$key]["con"] = $this->app->db->name("dmh_goods_stay")->where($wheres)->count();
			}
			if ($uid) {
				$stay = $this->app->db->name("dmh_goods_stay")->where("m_id", $v["m_id"])->where("u_id", $uid)->find();
				if ($stay) {
					$list[$key]["stay"] = 1;
				} else {
					$list[$key]["stay"] = 0;
				}
			}
		}
		$res["list"] = $list;
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function uids()
	{
		$res["uids"] = $this->request->uid;
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}