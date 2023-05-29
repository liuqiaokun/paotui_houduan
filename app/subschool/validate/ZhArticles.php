<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class ZhArticles extends \think\validate
{
	protected $rule = ["content" => ["require"]];
	protected $message = ["content.require" => "内容不能为空"];
	protected $scene = ["update" => ["content"]];
}