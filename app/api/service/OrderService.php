<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class OrderService extends \xhadmin\CommonService
{
	public static function takeExpressOrder($data)
	{
		try {
			$res = \app\api\model\Order::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function sendExpressOrder($data)
	{
		try {
			$res = \app\api\model\Order::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function helpBuyOrder($data)
	{
		try {
			$res = \app\api\model\Order::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function universalOrder($data)
	{
		try {
			$res = \app\api\model\Order::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function orderLstList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\Order::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
			foreach ($res["data"] as &$v) {
				$v = self::statusWhere($v);
			}
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function statusWhere($order)
	{
		$sex = ["不限", "男", "女"];
		$type = [1 => "取件", 2 => "寄件", 3 => "超市食堂", 4 => "万能任务"];
		$status = [1 => "未支付", 2 => "待接单", 3 => "待取货", 7 => "待送达", 4 => "已完成", 5 => "已过期", 6 => "未完成", 8 => "已取消", 9 => "取消中", 10 => "待到店", 11 => "已送达"];
		$order["_status_txt"] = $status[$order["status"]];
		$order["_type_txt"] = $type[$order["type"]];
		$order["_sex_limit_txt"] = $sex[$order["sex_limit"]];
		$order["_sh_sex_txt"] = $sex[$order["sh_sex"]];
		$order["_start_time"] = date("m-d H:i", $order["start_time"]);
		$order["create_time"] = date("m-d H:i", $order["createtime"]);
		$time = new \utils\Time();
		$order["_create_time"] = $time->timeDiff($order["createtime"]);
		$order["sh_address"] = \app\api\model\School::where(["s_id" => $order["s_id"]])->value("s_name") . $order["sh_address"];
		$order["start_user"] = \app\api\model\WechatUser::where(["openid" => $order["start_openid"], "wxapp_id" => $order["wxapp_id"]])->find();
		$order["end_user"] = [];
		if ($order["type"] == 3) {
			$order["qu_addres"] = \app\api\model\ZhBusiness::where(["business_id" => $order["store_id"]])->value("business_address");
		}
		if ($order["end_openid"]) {
			$order["end_user"] = \app\api\model\WechatUser::where(["openid" => $order["end_openid"], "wxapp_id" => $order["wxapp_id"]])->find();
		}
		return $order;
	}
}