<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class DmhsettledService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\Dmhsettled::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$data["createtime"] = time();
			$res = \app\api\model\Dmhsettled::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->d_id;
	}
	public static function update($where, $data)
	{
		try {
			!is_null($data["createtime"]) && ($data["createtime"] = strtotime($data["createtime"]));
			$data["updatetime"] = time();
			$res = \app\api\model\Dmhsettled::where($where)->update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
}