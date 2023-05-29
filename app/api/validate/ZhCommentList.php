<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class ZhCommentList extends \think\validate
{
	protected $rule = ["content" => ["require"]];
	protected $message = ["content.require" => "内容不能为空"];
	protected $scene = ["add" => ["content"]];
}