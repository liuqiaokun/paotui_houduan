<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class ZhBusinesType extends \think\validate
{
	protected $rule = ["type_name" => ["require"]];
	protected $message = ["type_name.require" => "分类名称不能为空"];
	protected $scene = ["add" => ["type_name"], "update" => ["type_name"]];
}