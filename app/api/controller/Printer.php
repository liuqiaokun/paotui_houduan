<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

include "typeset.php";
include "API_PHP_DEMO.php";
class Printer extends Common
{
	public function index($o_id)
	{
		$data = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
		$data["good_details"] = explode(";", $data["good_details"]);
		foreach ($data["good_details"] as $k => $v) {
			$temp = explode(" - ", $v);
			$v1["name"] = $temp[1];
			$v1["num"] = str_replace("份", "", $temp[2]);
			$v1["price"] = str_replace("元/份", "", $temp[3]);
			$v1["total"] = $v1["price"] * $v1["num"];
			$data["good_details"][$k] = $v1;
		}
		$store = \think\facade\Db::name("zh_business")->find($data["store_id"]);
		if ($store["printer_user"] && $store["printer"] && $store["printer_ukey"]) {
			$content = "<CB>***" . $setting["app_name"] . "***</CB><BR>";
			$content .= "<CB>" . $store["business_name"] . "</CB><BR>";
			if ($data["is_self_lifting"] == 1) {
				$content .= "<CB>#" . $data["pick_number"] . "【到店自取】</CB><BR>";
			} else {
				$content .= "<CB>#" . $data["pick_number"] . "</CB><BR>";
			}
			$content .= "名称　　　　　 单价  数量 金额<BR>";
			$content .= "--------------------------------<BR>";
			$content .= types($data["good_details"], 14, 6, 3, 6);
			$content .= "--------------------------------<BR>";
			$content .= "合计：" . $data["food_money"] . "元<BR>";
			$content .= "<BOLD>订单号：" . $data["ordersn"] . "</BOLD><BR>";
			if ($data["is_self_lifting"] == 0) {
				if ($data["givetype"] == 1) {
					$content .= "<BOLD>送达时间：" . $data["ordertime"] . "</BOLD><BR>";
				} else {
					$content .= "<BOLD>送达时间：" . date("Y-m-d H:i", $data["start_time"]) . "</BOLD><BR>";
				}
			} else {
				$content .= "<BOLD>到店时间：" . $data["arrival_time"] . "</BOLD><BR>";
			}
			if (!empty($data["remarks"])) {
				$content .= "备注：" . $data["remarks"] . "<BR>";
			}
			$content .= "用户姓名：" . $data["sh_name"] . "<BR>";
			if ($data["is_self_lifting"] == 0) {
				$content .= "用户手机号：" . $data["sh_phone"] . "<BR>";
				$content .= "收货地址：" . $data["sh_addres"] . "<BR>";
			} else {
				$content .= "用户手机号：" . $data["phone"] . "<BR>";
			}
			$content .= "下单时间：" . date("Y-m-d H:i:s", $data["createtime"]) . "<BR>";
			printMsg($store, $content, 1);
		}
	}
	public function cancel($o_id)
	{
		$data = \think\facade\Db::name("dmh_school_order")->find($o_id);
		$data["good_details"] = explode(",", $data["good_details"]);
		foreach ($data["good_details"] as $k => $v) {
			$temp = explode(" - ", $v);
			$v1["name"] = $temp[1];
			$v1["num"] = str_replace("份", "", $temp[2]);
			$v1["price"] = str_replace("元/份", "", $temp[3]);
			$v1["total"] = $v1["price"] * $v1["num"];
			$data["good_details"][$k] = $v1;
		}
		$store = \think\facade\Db::name("zh_business")->find($data["store_id"]);
		if ($store["printer_user"] && $store["printer"] && $store["printer_ukey"]) {
			$content = "<CB>" . $store["business_name"] . "</CB><BR>";
			$content .= "<CB>客户申请取消订单</CB><BR>";
			$content .= "<CB>#" . $data["pick_number"] . "</CB><BR>";
			$content .= "<BOLD>订单号：" . $data["ordersn"] . "</BOLD><BR>";
			if (!empty($data["desc"])) {
				$content .= "取消原因：" . $data["cancel_reason"] . "<BR>";
			}
			$content .= "合计：" . $data["food_money"] . "元<BR>";
			$content .= "取消时间：" . date("Y-m-d H:i:s") . "<BR>";
			printMsg($store, $content, 1);
		}
	}
}