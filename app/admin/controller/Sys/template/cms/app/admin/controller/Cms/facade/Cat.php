<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Cms\facade;

class Cat extends \think\Facade
{
	protected static function getFacadeClass()
	{
		return "app\\admin\\service\\Cms\\CataTreeService";
	}
}