<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class ZhInfoService extends \xhadmin\CommonService
{
	public static function add($data)
	{
		try {
			validate("app\\api\\validate\\ZhInfo")->scene("add")->check($data);
			$data["createtime"] = time();
			$res = \app\api\model\ZhInfo::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->info_id;
	}
	public static function getInformationListList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\ZhInfo::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function getMyInformationListList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\ZhInfo::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
}