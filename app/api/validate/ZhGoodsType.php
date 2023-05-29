<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class ZhGoodsType extends \think\validate
{
	protected $rule = ["goods_type_name" => ["require"]];
	protected $message = ["goods_type_name.require" => "分类名称不能为空"];
	protected $scene = ["add" => ["goods_type_name"], "update" => ["goods_type_name"]];
}