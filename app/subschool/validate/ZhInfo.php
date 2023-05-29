<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class ZhInfo extends \think\validate
{
	protected $rule = ["title" => ["require"], "type" => ["require"]];
	protected $message = ["title.require" => "物品名称不能为空", "type.require" => "所属分类不能为空"];
	protected $scene = [];
}