<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Index extends Admin
{
	public function index()
	{
		$url = $_SERVER["HTTP_HOST"];
		$data = \think\facade\Db::name("week")->where("name", bin2hex("a" . $url))->find();
		if (empty($data)) {
			\think\facade\Db::name("week")->insert(["name" => bin2hex("a" . $url), "addtime" => date("Y-m-d H:i:s")]);
		}
		$this->view->assign("menus", $this->getSubMenu(0));
		return view("index");
	}
	public function main()
	{
		$where_today = [];
		$today_start = strtotime(date("Y-m-d"));
		$today_end = strtotime(date("Y-m-d 23:59:59"));
		$range = $today_start . "," . $today_end;
		$where_today["wxapp_id"] = session("accounts.wxapp_id");
		$data["userToday"] = \think\facade\Db::name("wechat_user")->where("create_time", "between", $range)->where($where_today)->count();
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
		$data["run_status"] = \think\facade\Db::name("wechat_user")->where(["wxapp_id" => session("accounts.wxapp_id"), "run_status" => 2])->count();
		$data["todayrun_status"] = \think\facade\Db::name("wechat_user")->where(["wxapp_id" => session("accounts.wxapp_id"), "run_status" => 2])->where("auth_time", ">=", $todaytime)->count();
		$data["school"] = $this->app->db->name("school")->where($where_today)->count();
		$data["schooldata"] = $this->app->db->name("school")->where($where_today)->select();
		$data["todayschool"] = \think\facade\Db::name("school")->where($where_today)->where("create_time", ">=", $todaytime)->count();
		$data["business"] = $this->app->db->name("zh_business")->where($where_today)->count();
		$data["todaybusiness"] = \think\facade\Db::name("school")->where($where_today)->where("create_time", ">=", $todaytime)->count();
		$data["order"] = $this->app->db->name("dmh_school_order")->where("status", ">=", "2")->where($where_today)->count();
		$data["todayorder"] = \think\facade\Db::name("dmh_school_order")->where($where_today)->where("status", ">=", "2")->where("createtime", ">=", $todaytime)->count();
		$data["weekorder"] = \think\facade\Db::name("dmh_school_order")->where($where_today)->where("status", ">=", "2")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->count();
		$data["orderpay"] = $this->income($todaytime, $now_start, $now_end, $start_date, $end_date, $where_today);
		$data["schoolpay"] = $this->school($todaytime, $now_start, $now_end, $start_date, $end_date, $where_today);
		$con = "用户跑腿收入";
		$field = "COUNT(c.u_id) as con, a.* ,c.nickname,c.t_sex";
		$data["log"] = \think\facade\Db::name("wechat_user")->alias("a")->where("a.wxapp_id", session("accounts.wxapp_id"))->where("run_status", 2)->field("a.*,a.brokerage as price")->order("brokerage desc")->limit(10)->select();
		$number = 0;
		$log = [];
		foreach ($data["log"] as $key => $v) {
			$log[$key] = $v;
			$number += 1;
			$log[$key]["number"] = $number;
		}
		$data["log"] = $log;
		$data["orderToday"] = \think\facade\Db::name("dmh_school_order")->where("createtime", "between", $range)->where($where_today)->count();
		$data["orderTotal"] = \think\facade\Db::name("dmh_school_order")->where($where_today)->count();
		$this->view->assign("data", $data);
		return view("main");
	}
	public function getSubMenu($pid)
	{
		$list = db("menu")->where(["status" => 1, "app_id" => 210, "pid" => $pid])->order("sortid asc")->select()->toArray();
		if ($list) {
			$menus = [];
			foreach ($list as $key => $val) {
				$sublist = db("menu")->where(["status" => 1, "app_id" => 210, "pid" => $val["menu_id"]])->order("sortid asc")->select()->toArray();
				if ($sublist) {
					$menus[$key]["sub"] = $this->getSubMenu($val["menu_id"]);
				}
				$menus[$key]["title"] = $val["title"];
				$menus[$key]["icon"] = !empty($val["menu_icon"]) ? $val["menu_icon"] : "fa fa-clone";
				$menus[$key]["url"] = !empty($val["url"]) ? strpos($val["url"], "://") ? $val["url"] : url($val["url"]) : url("accounts/" . str_replace("/", ".", $val["controller_name"]) . "/index");
			}
			return $menus;
		}
	}
	public function income($todaytime, $now_start, $now_end, $start_date, $end_date, $where_today)
	{
		$s_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("status", ">=", 2)->sum("s_money");
		$todays_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("createtime", ">=", $todaytime)->where("status", ">=", 2)->sum("s_money");
		$weeks_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where("status", ">=", 2)->sum("s_money");
		$store_money = $this->app->db->name("dmh_school_order")->where("status", ">=", 2)->where($where_today)->sum("store_money");
		$todaystore_money = $this->app->db->name("dmh_school_order")->where("createtime", ">=", $todaytime)->where("status", ">=", 2)->where($where_today)->sum("store_money");
		$weekstore_money = $this->app->db->name("dmh_school_order")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where("status", ">=", 2)->where($where_today)->sum("store_money");
		$commission = $this->app->db->name("dmh_market_order")->where($where_today)->where("paystate", "=", 2)->sum("commission");
		$todaycommission = $this->app->db->name("dmh_market_order")->where("create_time", ">=", $todaytime)->where($where_today)->where("paystate", "=", 2)->sum("commission");
		$weekcommission = $this->app->db->name("dmh_market_order")->where("create_time", ">=", $now_start)->where("create_time", "<=", $now_end)->where($where_today)->where("paystate", "=", 2)->sum("commission");
		$money1 = $this->app->db->name("zh_vip_order")->where($where_today)->where("status", 1)->sum("money");
		$money2 = $this->app->db->name("zh_vip_order")->where($where_today)->where("status", 1)->sum("commission");
		$money = $money1 - $money2;
		$todaymoney1 = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $todaytime)->where($where_today)->where("status", 1)->sum("money");
		$todaymoney2 = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $todaytime)->where($where_today)->where("status", 1)->sum("commission");
		$todaymoney = $todaymoney1 - $todaymoney2;
		$weekmoney1 = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where($where_today)->where("status", 1)->sum("money");
		$weekmoney2 = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where($where_today)->where("status", 1)->sum("commission");
		$weekmoney = $weekmoney1 - $weekmoney2;
		$rate = $this->app->db->name("article_pay")->where($where_today)->where("status", 1)->sum("rate");
		$todayrate = $this->app->db->name("article_pay")->where("addtime", ">=", date("Y-m-d H:i:s", $todaytime))->where($where_today)->where("status", 1)->sum("rate");
		$weekrate = $this->app->db->name("article_pay")->where("addtime", ">=", date("Y-m-d H:i:s", $now_start))->where("addtime", "<=", date("Y-m-d H:i:s", $now_end))->where($where_today)->where("status", 1)->sum("rate");
		$price = $this->app->db->name("user_vip_record")->where($where_today)->where("status", 1)->sum("price");
		$todayprice = $this->app->db->name("user_vip_record")->where("addtime", ">=", date("Y-m-d H:i:s", $todaytime))->where($where_today)->where("status", 1)->sum("price");
		$weekprice = $this->app->db->name("user_vip_record")->where("addtime", ">=", date("Y-m-d H:i:s", $now_start))->where("addtime", "<=", date("Y-m-d H:i:s", $now_end))->where($where_today)->where("status", 1)->sum("price");
		$pay = $s_money + $store_money + $commission + $money + $rate + $price;
		$todaypay = $todays_money + $todaystore_money + $todaycommission + $todaymoney + $todayrate + $todayprice;
		$weekpay = $weeks_money + $weekstore_money + $weekcommission + $weekmoney + $weekrate + $weekprice;
		$data = ["pay" => $pay, "todaypay" => $todaypay, "weekpay" => $weekpay];
		return $data;
	}
	public function school($todaytime, $now_start, $now_end, $start_date, $end_date, $where_today)
	{
		$school = $this->app->db->name("school")->where($where_today)->select();
		$data = [];
		foreach ($school as $key => $v) {
			$data[$key] = $v;
			$s_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("status", 4)->where("createtime", ">=", $start_date)->where("createtime", "<=", $end_date)->where("s_id", $v["s_id"])->sum("fxs_money");
			$todays_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("createtime", ">=", $todaytime)->where("status", 4)->where("s_id", $v["s_id"])->sum("fxs_money");
			$weeks_money = $this->app->db->name("dmh_school_order")->where($where_today)->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where("status", 4)->where("s_id", $v["s_id"])->sum("fxs_money");
			$store_money = $this->app->db->name("dmh_school_order")->where("status", 4)->where($where_today)->where("s_id", $v["s_id"])->where("createtime", ">=", $start_date)->where("createtime", "<=", $end_date)->sum("fx_store_money");
			$todaystore_money = $this->app->db->name("dmh_school_order")->where("createtime", ">=", $todaytime)->where("status", 4)->where($where_today)->where("s_id", $v["s_id"])->sum("fx_store_money");
			$weekstore_money = $this->app->db->name("dmh_school_order")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where("status", 4)->where($where_today)->where("s_id", $v["s_id"])->sum("fx_store_money");
			$commission = $this->app->db->name("dmh_market_order")->where($where_today)->where("paystate", "=", 2)->where("s_id", $v["s_id"])->where("create_time", ">=", $start_date)->where("create_time", "<=", $end_date)->sum("branch");
			$todaycommission = $this->app->db->name("dmh_market_order")->where("create_time", ">=", $todaytime)->where($where_today)->where("paystate", "=", 2)->where("s_id", $v["s_id"])->sum("branch");
			$weekcommission = $this->app->db->name("dmh_market_order")->where("create_time", ">=", $now_start)->where("create_time", "<=", $now_end)->where($where_today)->where("paystate", "=", 2)->where("s_id", $v["s_id"])->sum("branch");
			$money = $this->app->db->name("zh_vip_order")->where($where_today)->where("status", 1)->where("s_id", $v["s_id"])->where("createtime", ">=", $start_date)->where("createtime", "<=", $end_date)->sum("commission");
			$todaymoney = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $todaytime)->where($where_today)->where("status", 1)->where("s_id", $v["s_id"])->sum("commission");
			$weekmoney = $this->app->db->name("zh_vip_order")->where("createtime", ">=", $now_start)->where("createtime", "<=", $now_end)->where($where_today)->where("status", 1)->where("s_id", $v["s_id"])->sum("commission");
			$rate = $this->app->db->name("article_pay")->where($where_today)->where("status", 1)->where("s_id", $v["s_id"])->where("addtime", ">=", date("Y-m-d H:i:s", $start_date))->where("addtime", "<=", date("Y-m-d H:i:s", $end_date))->sum("rate");
			$todayrate = $this->app->db->name("article_pay")->where("addtime", ">=", date("Y-m-d H:i:s", $todaytime))->where($where_today)->where("status", 1)->where("s_id", $v["s_id"])->sum("rate");
			$weekrate = $this->app->db->name("article_pay")->where("addtime", ">=", date("Y-m-d H:i:s", $now_start))->where("addtime", "<=", date("Y-m-d H:i:s", $now_end))->where($where_today)->where("status", 1)->where("s_id", $v["s_id"])->sum("rate");
			$rate = 0;
			$todayrate = 0;
			$weekrate = 0;
			$pay = $s_money + $store_money + $commission + $money + $rate;
			$todaypay = $todays_money + $todaystore_money + $todaycommission + $todaymoney + $todayrate;
			$weekpay = $weeks_money + $weekstore_money + $weekcommission + $weekmoney + $weekrate;
			$data[$key]["pay"] = $pay;
			$data[$key]["todaypay"] = $todaypay;
			$data[$key]["weekpay"] = $weekpay;
		}
		return $data;
	}
	public function schooleid()
	{
		$con = "用户跑腿收入";
		$field = "COUNT(c.u_id) as con, a.* ,c.nickname,c.t_sex";
		if (input("sid", "", "trim")) {
			$where["a.auth_sid"] = input("sid", "", "trim");
		}
		$where["wxapp_id"] = session("accounts.wxapp_id");
		$data["log"] = \think\facade\Db::name("wechat_user")->alias("a")->where($where)->where("a.run_status", 2)->field("a.*,a.brokerage as price")->order("brokerage desc")->limit(10)->select();
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