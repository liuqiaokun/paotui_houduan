<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class DmhstudentService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\Dmhstudent::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$data["create_time"] = time();
			$res = \app\api\model\Dmhstudent::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function update($where, $data)
	{
		try {
			!is_null($data["create_time"]) && ($data["create_time"] = strtotime($data["create_time"]));
			$res = \app\api\model\Dmhstudent::where($where)->update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
}