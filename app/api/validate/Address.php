<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class Address extends \think\validate
{
	protected $rule = ["name" => ["require"], "sex" => ["require"], "phone" => [0 => "require", "regex" => "/^1[3456789]\\d{9}\$/"], "s_id" => ["require"], "addres" => ["require"]];
	protected $message = ["name.require" => "联系人姓名不能为空", "sex.require" => "性别不能为空", "phone.require" => "手机号不能为空", "phone.regex" => "手机号有误", "s_id.require" => "学校id不能为空", "addres.require" => "详情地址不能为空"];
	protected $scene = ["add" => ["name", "sex", "phone", "s_id", "addres"], "update" => ["name", "sex", "phone", "s_id", "addres"]];
}