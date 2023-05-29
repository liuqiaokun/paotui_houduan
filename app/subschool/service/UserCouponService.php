<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class UserCouponService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("user_coupon")->field($field)->alias("a")->join("wechat_user b", "a.u_id=b.u_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
}