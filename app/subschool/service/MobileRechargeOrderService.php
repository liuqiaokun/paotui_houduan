<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class MobileRechargeOrderService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("mobile_recharge_order")->field($field)->alias("a")->join("wechat_user b", "a.u_id=b.u_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function update($data)
	{
		try {
			$res = \app\subschool\model\MobileRechargeOrder::update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res;
	}
}