<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class MobileRechargeOrderService extends \xhadmin\CommonService
{
	public static function add($data)
	{
		try {
			$data["createtime"] = time();
			$res = \app\api\model\MobileRechargeOrder::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->oid;
	}
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\MobileRechargeOrder::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
}