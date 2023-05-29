<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class UserCoupon extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["u_id"] = $this->request->uid;
		$where["s_id"] = $this->request->post("s_id", "", "serach_in");
		$where["use_status"] = $this->request->post("use_status", "", "serach_in");
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\UserCouponService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function adds()
	{
		$postField = "o_id,u_id,s_id,wxapp_id,use_status,create_time,update_time";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$res = \app\api\service\UserCouponService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function add()
	{
		$postField = "o_id,u_id,s_id,wxapp_id,use_status,create_time,update_time";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$data["use_status"] = 0;
		$temp = \think\facade\Db::name("user_coupon")->where("s_id", $data["s_id"])->where("u_id", $data["u_id"])->where("o_id", $data["o_id"])->find();
		if ($temp) {
			return $this->ajaxReturn($this->errorCode, "请勿重复领取", 0);
		}
		$coupon = \think\facade\Db::name("coupon")->find($data["o_id"]);
		$data["c_name"] = $coupon["c_name"];
		$data["price"] = $coupon["price"];
		$data["type"] = $coupon["type"];
		$data["cut_num"] = $coupon["cut_num"];
		$res = \app\api\service\UserCouponService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function couponindex()
	{
		$s_id = $this->request->post("s_id", "", "serach_in");
		$u_id = $this->request->uid;
		$school = \think\facade\Db::name("school")->find($s_id);
		$coupon = $school["coupon_list"] ? explode(",", $school["coupon_list"]) : [];
		$have = \think\facade\Db::name("user_coupon")->where("s_id", $s_id)->where("u_id", $u_id)->column("o_id");
		$couponList = [];
		foreach ($coupon as &$v) {
			if (!in_array($v, $have)) {
				$info = \think\facade\Db::name("coupon")->find($v);
				if ($info["status"] == 1) {
					$couponList[] = $info;
				}
			}
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $couponList);
	}
}