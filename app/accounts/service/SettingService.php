<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\service;

class SettingService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\accounts\model\Setting::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\accounts\\validate\\Setting")->scene("add")->check($data);
			$data["wxapp_id"] = session("accounts.wxapp_id");
			$res = \app\accounts\model\Setting::create($data);
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
			validate("app\\accounts\\validate\\Setting")->scene("update")->check($data);
			$data["wxapp_id"] = session("accounts.wxapp_id");
			$res = \app\accounts\model\Setting::update($data);
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