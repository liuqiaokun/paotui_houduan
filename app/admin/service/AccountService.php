<?php

//decode by http://www.yunlu99.com/
namespace app\admin\service;

class AccountService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\admin\model\Account::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\admin\\validate\\Account")->scene("add")->check($data);
			$data["create_time"] = time();
			$res = \app\admin\model\Account::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->wxapp_id;
	}
	public static function update($data)
	{
		try {
			validate("app\\admin\\validate\\Account")->scene("update")->check($data);
			$res = \app\admin\model\Account::update($data);
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