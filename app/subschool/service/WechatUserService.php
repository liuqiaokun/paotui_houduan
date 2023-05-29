<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class WechatUserService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("wechat_user")->field($field)->alias("a")->join("school b", "a.s_id=b.s_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
}