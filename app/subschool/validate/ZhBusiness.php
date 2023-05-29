<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\validate;

class ZhBusiness extends \think\validate
{
	protected $rule = ["wxadmin_name" => ["unique:zh_business"], "business_name" => ["require"], "business_address" => ["require"], "phone" => ["regex" => "/^1[3456789]\\d{9}\$/"], "business_image" => ["require"], "status" => ["require"], "type" => ["require"], "delivery_fee" => ["regex" => "/(^[1-9]([0-9]+)?(\\.[0-9]{1,2})?\$)|(^(0){1}\$)|(^[0-9]\\.[0-9]([0-9])?\$)/"], "store_money" => ["regex" => "/^([1-9]\\d|\\d)\$/"], "fx_store_money" => ["regex" => "/^([1-9]\\d|\\d)\$/"]];
	protected $message = ["wxadmin_name.unique" => "微信管理员已经存在", "business_name.require" => "商家名称不能为空", "business_address.require" => "商家地址不能为空", "phone.regex" => "手机号有误", "business_image.require" => "商家图片不能为空", "status.require" => "营业状态不能为空", "type.require" => "商家类型不能为空", "delivery_fee.regex" => "配送费格式错误", "store_money.regex" => "请输入0-100的整数", "fx_store_money.regex" => "请输入0-100的整数"];
	protected $scene = ["add" => ["wxadmin_name", "business_name", "business_address", "phone", "business_image", "status", "type", "delivery_fee", "store_money", "fx_store_money"], "update" => ["wxadmin_name", "business_name", "business_address", "phone", "business_image", "status", "type", "delivery_fee", "store_money", "fx_store_money"]];
}