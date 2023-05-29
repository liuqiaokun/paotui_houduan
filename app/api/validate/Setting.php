<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class Setting extends \think\validate
{
	protected $rule = ["wxapp_id" => ["require"], "appid" => ["require"], "step_price" => ["regex" => "/^(([1-9][0-9]*)|(([0]\\.\\d{1,2}|[1-9][0-9]*\\.\\d{1,2})))\$/"], "start_fee" => ["regex" => "/^(([1-9][0-9]*)|(([0]\\.\\d{1,2}|[1-9][0-9]*\\.\\d{1,2})))\$/"], "withdraw_min" => ["regex" => "/^(([1-9][0-9]*)|(([0]\\.\\d{1,2}|[1-9][0-9]*\\.\\d{1,2})))\$/"]];
	protected $message = ["wxapp_id.require" => "平台id不能为空", "appid.require" => "小程序appid不能为空", "step_price.regex" => "请输入正确的金额", "start_fee.regex" => "请输入正确的金额", "withdraw_min.regex" => "请输入正确的金额"];
	protected $scene = [];
}