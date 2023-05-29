<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\validate;

class Coupon extends \think\validate
{
	protected $rule = ["c_name" => ["require"], "price" => [0 => "require", "regex" => "/^(([1-9][0-9]*)|(([0]\\.\\d{1,2}|[1-9][0-9]*\\.\\d{1,2})))\$/"], "cut_num" => ["regex" => "/^(([1-9][0-9]*)|(([0]\\.\\d{1,2}|[1-9][0-9]*\\.\\d{1,2})))\$/"]];
	protected $message = ["c_name.require" => "优惠券名称不能为空", "price.require" => "金额不能为空", "price.regex" => "金额输入错误", "cut_num.regex" => "金额输入错误"];
	protected $scene = ["add" => ["price", "c_name", "cut_num"], "update" => ["c_name", "price", "cut_num"]];
}