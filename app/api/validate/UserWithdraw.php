<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class UserWithdraw extends \think\validate
{
	protected $rule = ["account" => ["require"], "name" => ["require"], "price" => [0 => "require", "regex" => "/(^[1-9]\\d*(\\.\\d{1,2})?\$)|(^0(\\.\\d{1,2})?\$)/"]];
	protected $message = ["account.require" => "提现账号不能为空", "name.require" => "提现姓名不能为空", "price.require" => "提现金额不能为空", "price.regex" => "提现金额格式错误"];
	protected $scene = ["add" => ["account", "name", "price"], "wx" => ["price"]];
}