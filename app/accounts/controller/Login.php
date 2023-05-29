<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Login extends Admin
{
	public function index()
	{
		if (!$this->request->isPost()) {
			return view("index");
		} else {
			$postField = "username,password,verify";
			$data = $this->request->only(explode(",", $postField), "post", null);
			if (!captcha_check($data["verify"])) {
				throw new \think\exception\ValidateException("验证码错误");
			}
			if ($this->checkLogin($data)) {
				$this->success("登录成功", url("accounts/Index/index"));
			}
		}
	}
	public function checkLogin($data)
	{
		$where["account"] = $data["username"];
		$where["pwd"] = md5($data["password"] . config("my.password_secrect"));
		try {
			$info = db("account")->where($where)->find();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$info) {
			throw new \think\exception\ValidateException("请检查用户名或者密码");
		}
		if (!$info["role_id"]) {
			$info["role_id"] = 1;
		}
		if (!$info["username"]) {
			$info["username"] = $info["account"];
		}
		session("accounts", $info);
		session("accounts_sign", data_auth_sign($info));
		event("LoginLog", $info);
		return true;
	}
	public function verify()
	{
		ob_clean();
		return captcha();
	}
	public function out()
	{
		session("accounts", null);
		return redirect(url("accounts/Login/index"));
	}
}