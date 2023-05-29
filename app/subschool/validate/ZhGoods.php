<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class ZhGoods extends \think\validate
{
	protected $rule = ["goods_type_id" => ["require"], "business_id" => ["require"], "goods_name" => ["require"]];
	protected $message = ["goods_type_id.require" => "所属分类不能为空", "business_id.require" => "所属商家不能为空", "goods_name.require" => "商品名称不能为空"];
	protected $scene = ["add" => ["goods_type_id", "goods_name"], "update" => ["goods_type_id", "goods_name"]];
}