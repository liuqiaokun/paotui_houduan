<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class Slide extends \think\validate
{
	protected $rule = ["name" => ["require"], "img" => ["require"]];
	protected $message = ["name.require" => "名称不能为空", "img.require" => "图片不能为空"];
	protected $scene = ["add" => ["name", "img"], "update" => ["name", "img"]];
}