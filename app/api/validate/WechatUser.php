<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class WechatUser extends \think\validate
{
	protected $rule = ["t_sex" => ["require"], "auth_sid" => ["require"], "t_name" => ["require"], "phone" => [0 => "require", "regex" => "/^1[3456789]\\d{9}\$/"]];
	protected $message = ["t_sex.require" => "性别不能为空", "auth_sid.require" => "认证学校id不能为空", "t_name.require" => "姓名不能为空", "phone.require" => "手机号不能为空", "phone.regex" => "手机号格式错误"];
	protected $scene = ["update" => ["t_sex", "auth_sid", "t_name", "phone"]];
}