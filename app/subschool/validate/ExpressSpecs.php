<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class ExpressSpecs extends \think\validate
{
	protected $rule = ["price" => ["require"]];
	protected $message = ["price.require" => "价格不能为空"];
	protected $scene = ["add" => ["price"], "update" => ["price"]];
}