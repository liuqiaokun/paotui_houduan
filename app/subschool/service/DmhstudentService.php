<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class DmhstudentService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\subschool\model\Dmhstudent::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$data["create_time"] = time();
			$data["s_id"] = session("subschool.s_id");
			$data["wxapp_id"] = session("subschool.wxapp_id");
			$res = \app\subschool\model\Dmhstudent::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->id;
	}
	public static function update($data)
	{
		try {
			$data["create_time"] = strtotime($data["create_time"]);
			$data["s_id"] = session("subschool.s_id");
			$data["wxapp_id"] = session("subschool.wxapp_id");
			$res = \app\subschool\model\Dmhstudent::update($data);
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