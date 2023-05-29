<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class ZhForumClass extends \think\validate
{
	protected $rule = ["class_name" => ["require"]];
	protected $message = ["class_name.require" => "分类名称不能为空"];
	protected $scene = [];
}