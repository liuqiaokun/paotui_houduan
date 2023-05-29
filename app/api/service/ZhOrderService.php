<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class ZhOrderService extends \xhadmin\CommonService
{
	public static function getNewestOrderLstList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\ZhOrder::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function getHistoryOrderListList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\ZhOrder::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
}