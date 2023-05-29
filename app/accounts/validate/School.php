<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\validate;

class School extends \think\validate
{
	protected $rule = ["logo" => ["require"], "province" => ["require"], "city" => ["require"], "plat_rate" => [0 => "require", "regex" => "/^([1-9]\\d|\\d)\$/"], "school_rate" => [0 => "require", "regex" => "/^([1-9]\\d|\\d)\$/"], "second_rate" => [0 => "require", "regex" => "/^([1-9]\\d|\\d)\$/"], "fx_second_rate" => [0 => "require", "regex" => "/^[0-9]*\$/"], "edit_status" => ["require"], "reward_rate" => [0 => "require", "regex" => "/^([1-9]\\d|\\d)\$/"], "store_rate" => [0 => "require", "regex" => "/^([1-9]\\d|\\d)\$/"], "fx_store_rate" => [0 => "require", "regex" => "/^([1-9]\\d|\\d)\$/"], "level1" => ["regex" => "/^([1-9]\\d|\\d)\$/"], "level2" => ["regex" => "/^([1-9]\\d|\\d)\$/"]];
	protected $message = ["logo.require" => "学校logo不能为空", "province.require" => "所属省不能为空", "city.require" => "所属市不能为空", "plat_rate.require" => "跑腿平台抽成不能为空", "plat_rate.regex" => "请输入0-100的整数", "school_rate.require" => "跑腿学校抽成不能为空", "school_rate.regex" => "请输入0-100的整数", "second_rate.require" => "二手市场平台抽成不能为空", "second_rate.regex" => "请输入0-100的整数", "fx_second_rate.require" => "二手市场分校抽成不能为空", "fx_second_rate.regex" => "输入错误", "edit_status.require" => "是否允许分校修改抽佣不能为空", "reward_rate.require" => "赏金抽成不能为空", "reward_rate.regex" => "请输入0-100的整数", "store_rate.require" => "商家平台抽成不能为空", "store_rate.regex" => "请输入0-100的整数", "fx_store_rate.require" => "商家分校抽成不能为空", "fx_store_rate.regex" => "请输入0-100的整数", "level1.regex" => "请输入整数", "level2.regex" => "请输入整数"];
	protected $scene = ["add" => ["logo", "province", "city", "plat_rate", "school_rate", "second_rate", "fx_second_rate", "edit_status", "reward_rate", "store_rate", "fx_store_rate"], "update" => ["logo", "province", "city", "plat_rate", "school_rate", "second_rate", "fx_second_rate", "edit_status", "reward_rate", "store_rate", "fx_store_rate"], "updateProject" => ["level1", "level2"]];
}