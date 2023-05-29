<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class TopSetting extends \think\validate
{
	protected $rule = ["price" => [0 => "require", "regex" => "/(^[1-9]([0-9]+)?(\\.[0-9]{1,2})?\$)|(^(0){1}\$)|(^[0-9]\\.[0-9]([0-9])?\$)/"], "day" => [0 => "require", "regex" => "/^[0-9]*\$/"]];
	protected $message = ["price.require" => "金额不能为空", "price.regex" => "金额格式错误", "day.require" => "置顶天数不能为空", "day.regex" => "置顶天数格式错误"];
	protected $scene = [];
}