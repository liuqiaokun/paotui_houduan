<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class Test extends Admin
{
	public function index()
	{
		if (!$this->request->isPost()) {
			return view("index");
		} else {
			$postField = "username,password,verify";
			$data = $this->request->only(explode(",", $postField), "post", null);
			if (!captcha_check($data["verify"])) {
				throw new ValidateException("验证码错误");
			}
			if ($this->checkLogin($data)) {
				$this->success("登录成功", url("admin/Index/index"));
			}
		}
	}
}