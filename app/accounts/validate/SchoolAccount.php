<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\validate;

class SchoolAccount extends \think\validate
{
	protected $rule = ["s_id" => ["require"], "account" => ["require", "unique:school_account"], "pwd" => ["require"]];
	protected $message = ["s_id.require" => "管理学校不能为空", "account.require" => "账号不能为空", "account.unique" => "账号已经存在", "pwd.require" => "密码不能为空"];
	protected $scene = ["add" => ["pwd", "s_id", "account"], "update" => ["s_id", "account", "pwd"]];
}