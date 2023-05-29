<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

class Error
{
	public function __call($method, $args)
	{
		throw new \think\exception\ClassNotFoundException("控制器不存在", request()->controller());
	}
}