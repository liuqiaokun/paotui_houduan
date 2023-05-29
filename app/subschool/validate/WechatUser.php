<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class WechatUser extends \think\validate
{
	protected $rule = ["phone" => ["regex" => "/^1[3456789]\\d{9}\$/"]];
	protected $message = ["phone.regex" => "手机号格式错误"];
	protected $scene = [];
}