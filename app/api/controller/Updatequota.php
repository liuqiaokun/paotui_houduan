<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Updatequota extends Common
{
	public function change()
	{
		\think\facade\Db::name("zh_goods")->where("quota", 2)->update(["quota" => 999999]);
	}
}