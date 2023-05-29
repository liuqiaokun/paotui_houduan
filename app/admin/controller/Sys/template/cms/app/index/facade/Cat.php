<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\facade;

class Cat extends \think\Facade
{
	protected static function getFacadeClass()
	{
		return "app\\ApplicationName\\service\\CatagoryService";
	}
}