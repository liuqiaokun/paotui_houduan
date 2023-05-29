<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class ZhCommenes extends \think\validate
{
	protected $rule = ["article_id" => ["require"]];
	protected $message = ["article_id.require" => "所属文章不能为空"];
	protected $scene = ["add" => ["article_id"]];
}