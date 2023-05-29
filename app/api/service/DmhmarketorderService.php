<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class DmhmarketorderService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\Dmhmarketorder::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function litList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\Dmhmarketorder::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$data["create_time"] = time();
			$res = \app\api\model\Dmhmarketorder::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
}