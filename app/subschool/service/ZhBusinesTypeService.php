<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class ZhBusinesTypeService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\subschool\model\ZhBusinesType::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhBusinesType")->scene("add")->check($data);
			$data["createtime"] = time();
			$res = \app\subschool\model\ZhBusinesType::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->type_id;
	}
	public static function update($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhBusinesType")->scene("update")->check($data);
			$data["createtime"] = strtotime($data["createtime"]);
			$res = \app\subschool\model\ZhBusinesType::update($data);
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