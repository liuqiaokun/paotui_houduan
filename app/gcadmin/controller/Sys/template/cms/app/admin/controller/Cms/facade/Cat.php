<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Cms\facade;

class Cat extends \think\Facade
{
	protected static function getFacadeClass()
	{
		return "app\\gcadmin\\service\\Cms\\CataTreeService";
	}
}