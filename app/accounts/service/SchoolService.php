<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\service;

class SchoolService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\accounts\model\School::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\accounts\\validate\\School")->scene("add")->check($data);
			$data["wxapp_id"] = session("accounts.wxapp_id");
			$data["coupon_list"] = implode(",", $data["coupon_list"]);
			$data["create_time"] = time();
			$res = \app\accounts\model\School::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->s_id;
	}
	public static function update($data)
	{
		try {
			validate("app\\accounts\\validate\\School")->scene("update")->check($data);
			$data["coupon_list"] = implode(",", $data["coupon_list"]);
			$res = \app\accounts\model\School::update($data);
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
	public static function updateProject($data)
	{
		try {
			$res = \app\accounts\model\School::update($data);
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