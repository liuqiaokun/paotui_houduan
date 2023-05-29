<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\validate;

class Setting extends \think\validate
{
	protected $rule = ["appid" => ["require"], "step_price" => ["regex" => "/^(([1-9][0-9]*)|(([0]\\.\\d{1,2}|[1-9][0-9]*\\.\\d{1,2})))\$/"], "start_fee" => ["regex" => "/^(([1-9][0-9]*)|(([0]\\.\\d{1,2}|[1-9][0-9]*\\.\\d{1,2})))\$/"], "withdraw_min" => ["regex" => "/^(([1-9][0-9]*)|(([0]\\.\\d{1,2}|[1-9][0-9]*\\.\\d{1,2})))\$/"], "order_finish_time" => ["regex" => "/^[0-9]*\$/"]];
	protected $message = ["appid.require" => "小程序appid不能为空", "step_price.regex" => "请输入正确的金额", "start_fee.regex" => "请输入正确的金额", "withdraw_min.regex" => "请输入正确的金额", "order_finish_time.regex" => "请输入整数"];
	protected $scene = ["add" => ["appid", "step_price", "start_fee", "withdraw_min", "order_finish_time"], "update" => ["appid", "step_price", "start_fee", "withdraw_min", "order_finish_time"]];
}