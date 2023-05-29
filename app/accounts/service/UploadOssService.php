<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\service;

class UploadOssService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\accounts\model\UploadOss::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$data["wxapp_id"] = session("accounts.wxapp_id");
			$data["create_time"] = time();
			$res = \app\accounts\model\UploadOss::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->u_id;
	}
	public static function update($data)
	{
		try {
			$data["wxapp_id"] = session("accounts.wxapp_id");
			$data["create_time"] = strtotime($data["create_time"]);
			$res = \app\accounts\model\UploadOss::update($data);
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