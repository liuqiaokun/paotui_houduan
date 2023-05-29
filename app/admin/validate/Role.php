<?php

//decode by http://www.yunlu99.com/
namespace app\admin\validate;

class Role extends \think\validate
{
	protected $rule = ["name" => ["require"]];
	protected $message = ["name.require" => "角色不能为空"];
	protected $scene = ["add" => ["name"], "update" => ["name"]];
}