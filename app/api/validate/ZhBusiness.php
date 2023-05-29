<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class ZhBusiness extends \think\validate
{
	protected $rule = ["business_name" => ["require"], "business_address" => ["require"], "status" => ["require"]];
	protected $message = ["business_name.require" => "商家名称不能为空", "business_address.require" => "商家地址不能为空", "status.require" => "营业状态不能为空"];
	protected $scene = ["update" => ["business_name", "business_address", "status"]];
}