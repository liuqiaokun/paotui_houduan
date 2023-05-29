<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\validate;

class Account extends \think\validate
{
	protected $rule = ["account" => ["require", "unique:account"], "pwd" => ["require"]];
	protected $message = ["account.require" => "登录账号不能为空", "account.unique" => "登录账号已经存在", "pwd.require" => "登录密码不能为空"];
	protected $scene = ["add" => ["account", "pwd"], "update" => ["account", "pwd"]];
}