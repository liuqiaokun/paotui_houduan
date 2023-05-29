<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Dmhschoolcarry extends Admin
{
	public function index()
	{
		if (!$this->request->isAjax()) {
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["wxapp_id"] = session("subschool.wxapp_id");
			$where["s_id"] = session("subschool.s_id");
			$where["alipay_name"] = $this->request->param("alipay_name", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,state,pay,alipay_name,alipay_account,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\subschool\service\DmhschoolcarryService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function carry()
	{
		$s_id = session("subschool")["s_id"];
		$data["pt_money"] = $this->app->db->name("dmh_school_order")->where("s_id", $s_id)->where("status", 4)->sum("fxs_money");
		$data["store_money"] = $this->app->db->name("dmh_school_order")->where("s_id", $s_id)->where("status", 4)->where("type", 3)->sum("fx_store_money");
		$data["pay"] = $this->app->db->name("dmh_school_carry")->where("state", "in", [0, 1])->where("s_id", $s_id)->sum("pay");
		$data["branch"] = $this->app->db->name("dmh_market_order")->where("s_id", $s_id)->where("paystate", 2)->sum("branch");
		$data["jtkmoney"] = $this->app->db->name("webview_order")->where("s_id", $s_id)->sum("schoolmoney");
		$data["bean"] = $this->app->db->name("zh_vip_order")->where("status", 1)->where("s_id", $s_id)->sum("commission");
		$info = $data["pt_money"] + $data["store_money"] + $data["branch"] + $data["bean"] + $data["jtkmoney"] - $data["pay"];
		$data["sum"] = $data["pt_money"] + $data["store_money"] + $data["branch"] + $data["bean"] + $data["jtkmoney"];
		$this->view->assign("money", $info);
		$this->view->assign("data", $data);
		return view("carry");
	}
	public function carry_ins()
	{
		$s_id = session("subschool")["s_id"];
		$wxapp_id = session("subschool")["wxapp_id"];
		$tpay = input("pay", "", "trim");
		$school = $this->app->db->name("school")->where("s_id", $s_id)->find();
		if (!$school["alipay_name"] || !$school["alipay_account"]) {
			return json(["status" => "01", "msg" => "请先在学校管理完善支付宝信息"]);
		}
		$branch = $this->app->db->name("dmh_market_order")->where("s_id", $s_id)->where("paystate", 2)->sum("branch");
		$money = $this->app->db->name("dmh_school_order")->where("s_id", $s_id)->where("status", 4)->sum("fxs_money");
		$store_money = $this->app->db->name("dmh_school_order")->where("s_id", $s_id)->where("status", 4)->where("type", 3)->sum("fx_store_money");
		$totalpay = $branch + $money + $store_money;
		$pay = $this->app->db->name("dmh_school_carry")->where("s_id", $s_id)->where("state", "0")->sum("pay");
		$pays = $this->app->db->name("dmh_school_carry")->where("state", "1")->where("s_id", $s_id)->sum("pay");
		$info = $totalpay - ($pay + $pays);
		if ($info < $tpay) {
			return json(["status" => "01", "msg" => "提现金额不能大于实际余额"]);
		}
		if (number_format($tpay, 2) <= 0.0) {
			return json(["status" => "01", "msg" => "提现金额不正确"]);
		}
		$data = ["s_id" => $s_id, "wxapp_id" => $wxapp_id, "state" => 0, "pay" => $tpay, "alipay_name" => $school["alipay_name"], "alipay_account" => $school["alipay_account"], "create_time" => time()];
		$order = $this->app->db->name("dmh_school_carry")->insert($data);
		if ($order) {
			return json(["status" => "00", "msg" => "提交成功"]);
		} else {
			return json(["status" => "01", "msg" => "提交失败"]);
		}
	}
}