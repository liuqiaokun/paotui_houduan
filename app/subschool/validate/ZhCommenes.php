<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class ZhCommenes extends \think\validate
{
	protected $rule = ["article_id" => ["require"]];
	protected $message = ["article_id.require" => "所属文章不能为空"];
	protected $scene = [];
}