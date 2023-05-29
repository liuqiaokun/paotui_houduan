<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class MobileRechargeService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\subschool\model\MobileRecharge::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$data["wxapp_id"] = session("subschool.wxapp_id");
			$data["s_id"] = session("subschool.s_id");
			$data["createtime"] = time();
			$res = \app\subschool\model\MobileRecharge::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->mid;
	}
	public static function update($data)
	{
		try {
			$data["wxapp_id"] = session("subschool.wxapp_id");
			$data["s_id"] = session("subschool.s_id");
			$res = \app\subschool\model\MobileRecharge::update($data);
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