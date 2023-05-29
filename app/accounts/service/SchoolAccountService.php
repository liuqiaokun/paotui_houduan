<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\service;

class SchoolAccountService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("school_account")->field($field)->alias("a")->join("school b", "a.s_id=b.s_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\accounts\\validate\\SchoolAccount")->scene("add")->check($data);
			$data["wxapp_id"] = session("accounts.wxapp_id");
			$data["create_time"] = time();
			$res = \app\accounts\model\SchoolAccount::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->a_id;
	}
	public static function update($data)
	{
		try {
			validate("app\\accounts\\validate\\SchoolAccount")->scene("update")->check($data);
			$data["wxapp_id"] = session("accounts.wxapp_id");
			$res = \app\accounts\model\SchoolAccount::update($data);
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