<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class UserCoupon extends Admin
{
	public function index()
	{
		if (!$this->request->isAjax()) {
			$school = \think\facade\Db::name("school")->find(session("subschool.s_id"));
			$coupon_list = explode(",", $school["coupon_list"]);
			$coupon_array = [];
			foreach ($coupon_list as $k => $v) {
				$coupon = \think\facade\Db::name("coupon")->find($v);
				$coupon_array[$k]["o_id"] = $coupon["o_id"];
				$coupon_array[$k]["c_name"] = $coupon["c_name"];
			}
			$this->view->assign("coupon_array", $coupon_array);
			$where[] = ["s_id", "=", session("subschool.s_id")];
			$where[] = ["status", "=", "4"];
			$where[] = ["createtime", "between", [strtotime(date("Y-m-d ") . "00:00:00"), strtotime(date("Y-m-d ") . "23:59:59")]];
			$cut_sum = \think\facade\Db::name("dmh_school_order")->where($where)->sum("y_money");
			$this->view->assign("cut_sum", $cut_sum);
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["a.o_id"] = $this->request->param("o_id", "", "serach_in");
			$where["a.s_id"] = session("subschool.s_id");
			$where["a.wxapp_id"] = session("subschool.wxapp_id");
			$where["a.type"] = $this->request->param("type", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.nickname";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\UserCouponService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
}