<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class Index extends Admin
{
	public function index()
	{
		$this->view->assign("menus", $this->getSubMenu(0));
		return view("index");
	}
	public function main()
	{
		$school = \think\facade\Db::name("school")->find(session("subschool.s_id"));
		$where_today["s_id"] = session("subschool.s_id");
		$where_today["wxapp_id"] = session("subschool.wxapp_id");
		$todaytime = strtotime(date("Y-m-d", time()) . " 0:00:00");
		$date = date("Y-m-d");
		$first = 1;
		$w = date("w", strtotime($date));
		$now_start = strtotime(date("Y-m-d", strtotime("{$date} -" . ($w == 0 ? 6 : $w - $first) . " days")) . " 0:00:00");
		$start = date("Y-m-d", strtotime("{$date} -" . ($w == 0 ? 6 : $w - $first) . " days")) . " 0:00:00";
		$now_end = strtotime(date("Y-m-d", strtotime("{$start} +6 day")) . " 23:59:59");
		$start_date = strtotime(date("Y-m-d", mktime(0, 0, 0, date("m", strtotime($date)), 1)) . " 0:00:00");
		$end_date = strtotime(date("Y-m-d", mktime(23, 59, 59, date("m", strtotime($date)) + 1, 0)) . " 23:59:59");
		$data["userTotal"] = \think\facade\Db::name("wechat_user")->where($where_today)->count();
		$data["todayuser"] = \think\facade\Db::name("wechat_user")->where($where_today)->where("create_time", ">=", $todaytime)->count();
		$data["run_status"] = \think\facade\Db::name("wechat_user")->where(["s_id" => session("subschool.s_id"), "run_status" => 2])->count();
		$data["todayrun_status"] = \think\facade\Db::name("wechat_user")->where(["s_id" => session("subschool.s_id"), "run_status" => 2])->where("auth_time", ">=", $todaytime)->count();
		$data["business"] = $this->app->db->name("zh_business")->where($where_today)->count();
		$data["todaybusiness"] = \think\facade\Db::name("school")->where($where_today)->where("create_time", ">=", $todaytime)->count();
		$data["order"] = $this->app->db->name("dmh_school_order")->where("status", ">=", "2")->where($where_today)->count();
		$data["todayorder"] = \think\facade\Db::name("dmh_school_order")->where($where_today)->where("status", ">=", "2")->where("createtime", ">=", $todaytime)->count();
		$data["weekorder"] = \think\facade\Db::name("dmh_school_order")->where($where_today)->where("status", ">=", "2")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->count();
		$data["monthorder"] = \think\facade\Db::name("dmh_school_order")->where($where_today)->where("status", ">=", "2")->where("createtime", ">=", $start_date)->where("createtime", "<=", $end_date)->count();
		$data["orderpay"] = $this->income($todaytime, $now_start, $now_end, $start_date, $end_date, $where_today);
		$con = "用户跑腿收入";
		$field = "COUNT(a.u_id) as con, a.* ,c.price";
		$data["log"] = \think\facade\Db::name("wechat_user")->alias("a")->where("a.auth_sid", session("subschool.s_id"))->where("a.wxapp_id", session("subschool.wxapp_id"))->where("a.run_status", 2)->field("a.*,a.brokerage as price")->order("brokerage desc")->limit(10)->select();
		$number = 0;
		$log = [];
		foreach ($data["log"] as $key => $v) {
			$log[$key] = $v;
			$number += 1;
			$log[$key]["number"] = $number;
		}
		$data["log"] = $log;
		$this->view->assign("data", $data);
		return view("main");
	}
	public function getSubMenu($pid)
	{
		$list = db("menu")->where(["status" => 1, "app_id" => 211, "pid" => $pid])->order("sortid asc")->select()->toArray();
		if ($list) {
			$menus = [];
			foreach ($list as $key => $val) {
				$sublist = db("menu")->where(["status" => 1, "app_id" => 211, "pid" => $val["menu_id"]])->order("sortid asc")->select()->toArray();
				if ($sublist) {
					$menus[$key]["sub"] = $this->getSubMenu($val["menu_id"]);
				}
				$menus[$key]["title"] = $val["title"];
				$menus[$key]["icon"] = !empty($val["menu_icon"]) ? $val["menu_icon"] : "fa fa-clone";
				$menus[$key]["url"] = !empty($val["url"]) ? strpos($val["url"], "://") ? $val["url"] : url($val["url"]) : url("subschool/" . str_replace("/", ".", $val["controller_name"]) . "/index");
			}
			return $menus;
		}
	}
	public function income($todaytime, $now_start, $now_end, $start_date, $end_date, $where_today)
	{
		$s_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("status", 4)->sum("fxs_money");
		$todays_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("createtime", ">=", $todaytime)->where("status", 4)->sum("fxs_money");
		$weeks_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where("status", 4)->sum("fxs_money");
		$months_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("createtime", ">=", $start_date)->where("createtime", "<=", $end_date)->where("status", 4)->sum("fxs_money");
		$store_money = $this->app->db->name("dmh_school_order")->where("status", 4)->where($where_today)->sum("fx_store_money");
		$todaystore_money = $this->app->db->name("dmh_school_order")->where("createtime", ">=", $todaytime)->where("status", 4)->where($where_today)->sum("fx_store_money");
		$weekstore_money = $this->app->db->name("dmh_school_order")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where("status", 4)->where($where_today)->sum("fx_store_money");
		$monthstore_money = $this->app->db->name("dmh_school_order")->where("createtime", ">=", $start_date)->where("createtime", "<=", $end_date)->where("status", 4)->where($where_today)->sum("fx_store_money");
		$commission = $this->app->db->name("dmh_market_order")->where($where_today)->where("paystate", "=", 2)->sum("branch");
		$todaycommission = $this->app->db->name("dmh_market_order")->where("create_time", "=", $todaytime)->where($where_today)->where("paystate", "=", 2)->sum("branch");
		$weekcommission = $this->app->db->name("dmh_market_order")->where("create_time", ">=", $now_start)->where("create_time", "<=", $now_end)->where($where_today)->where("paystate", "=", 2)->sum("branch");
		$monthcommission = $this->app->db->name("dmh_market_order")->where("create_time", ">=", $start_date)->where("create_time", "<=", $end_date)->where($where_today)->where("paystate", "=", 2)->sum("branch");
		$money = $this->app->db->name("zh_vip_order")->where($where_today)->where("status", 1)->sum("commission");
		$todaymoney = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $todaytime)->where($where_today)->where("status", 1)->sum("commission");
		$weekmoney = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where($where_today)->where("status", 1)->sum("commission");
		$monthmoney = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $start_date)->where("createtime", "<=", $end_date)->where($where_today)->where("status", 1)->sum("commission");
		$rate = 0;
		$todayrate = 0;
		$weekrate = 0;
		$monthrate = 0;
		$pay = $s_money + $store_money + $commission + $money + $rate;
		$todaypay = $todays_money + $todaystore_money + $todaycommission + $todaymoney + $todayrate;
		$weekpay = $weeks_money + $weekstore_money + $weekcommission + $weekmoney + $weekrate;
		$monthpay = $months_money + $monthstore_money + $monthcommission + $monthmoney + $monthrate;
		$data = ["pay" => $pay, "todaypay" => $todaypay, "weekpay" => $weekpay, "monthpay" => $monthpay];
		return $data;
	}
	public function schooleid()
	{
		$todaytime = date("Y-m-d", time()) . " 0:00:00";
		$date = date("Y-m-d");
		$first = 1;
		$w = date("w", strtotime($date));
		$now_start = date("Y-m-d", strtotime("{$date} -" . ($w == 0 ? 6 : $w - $first) . " days")) . " 0:00:00";
		$start = date("Y-m-d", strtotime("{$date} -" . ($w == 0 ? 6 : $w - $first) . " days")) . " 0:00:00";
		$now_end = strtotime(date("Y-m-d", strtotime("{$start} +6 day")) . " 23:59:59");
		$start_date = date("Y-m-d", mktime(0, 0, 0, date("m", strtotime($date)), 1)) . " 0:00:00";
		$end_date = date("Y-m-d", mktime(23, 59, 59, date("m", strtotime($date)) + 1, 0)) . " 23:59:59";
		if (input("type", "", "trim") == 1) {
			$where[] = ["c.addtime", ">=", $todaytime];
		}
		if (input("type", "", "trim") == 2) {
			$where[] = ["c.addtime", ">=", $now_start];
			$where[] = ["c.addtime", "<=", $now_end];
		}
		if (input("type", "", "trim") == 3) {
			$where[] = ["c.addtime", ">=", $start_date];
			$where[] = ["c.addtime", "<=", $end_date];
		}
		$con = "用户跑腿收入";
		$field = "COUNT(a.u_id) as con, a.* ,c.price";
		$data["log"] = $this->app->db->name("wechat_user", "mysql")->field($field)->alias("a")->where("a.s_id", session("subschool.s_id"))->join("user_account_log c", "a.u_id = c.uid", "left")->where("c.remark", "like", "%" . $con . "%")->where($where)->group("a.u_id")->order("price desc")->limit(10)->select();
		$number = 0;
		$log = [];
		foreach ($data["log"] as $key => $v) {
			$log[$key] = $v;
			if ($v["t_sex"] == 1) {
				$log[$key]["t_sex"] = "男";
			} elseif ($v["t_sex"] == 2) {
				$log[$key]["t_sex"] = "女";
			} else {
				$log[$key]["t_sex"] = "未知";
			}
			$number += 1;
			$log[$key]["number"] = $number;
		}
		return json($log);
	}
}