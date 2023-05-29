<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class Reserve extends \think\validate
{
	protected $rule = ["list" => ["require"]];
	protected $message = ["list.require" => "预约人员不能为空"];
	protected $scene = ["add" => ["list"]];
}