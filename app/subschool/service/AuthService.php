<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class AuthService
{
	public static function checkLogin($user, $pwd)
	{
		$where["tempUsername"] = $user;
		$pwd = !!config("my.password_secrect") ? $pwd . config("my.password_secrect") : $pwd;
		$where["tempPassword"] = md5($pwd);
		$info = \think\facade\Db::name("tablename")->where($where)->find();
		if (!$info) {
			throw new \Exception("请检查用户名或者密码");
		}
		if (!$info["role"]) {
			$info["role"] = 1;
		}
		if (!$info["username"]) {
			$info["username"] = $info["tempUsername"];
		}
		session("subschool", $info);
		session("subschool_sign", data_auth_sign($info));
		event("AdminLogin", $info);
		return true;
	}
}