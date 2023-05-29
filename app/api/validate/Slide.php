<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class Slide extends \think\validate
{
	protected $rule = ["name" => ["require"], "s_id" => ["require"], "img" => ["require"]];
	protected $message = ["name.require" => "名称不能为空", "s_id.require" => "所属学校不能为空", "img.require" => "图片不能为空"];
	protected $scene = [];
}