<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class UserCoupon extends \think\validate
{
	protected $rule = ["o_id" => ["require"]];
	protected $message = ["o_id.require" => "优惠券id不能为空"];
	protected $scene = ["add" => ["o_id"]];
}