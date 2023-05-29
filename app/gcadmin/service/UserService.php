<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\service;

class UserService extends \xhadmin\CommonService
{
	public static function add($data)
	{
		try {
			validate("app\\gcadmin\\validate\\User")->scene("add")->check($data);
			$data["pwd"] = md5($data["pwd"] . config("my.password_secrect"));
			$data["role_id"] = implode(",", $data["role_id"]);
			$data["create_time"] = time();
			$res = \app\gcadmin\model\User::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->user_id;
	}
	public static function update($data)
	{
		try {
			validate("app\\gcadmin\\validate\\User")->scene("update")->check($data);
			$data["role_id"] = implode(",", $data["role_id"]);
			$data["create_time"] = strtotime($data["create_time"]);
			$res = \app\gcadmin\model\User::update($data);
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
	public static function updatePassword($data)
	{
		try {
			validate("app\\gcadmin\\validate\\User")->scene("updatePassword")->check($data);
			$data["pwd"] = md5($data["pwd"] . config("my.password_secrect"));
			$res = \app\gcadmin\model\User::update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
}