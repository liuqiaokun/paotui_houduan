<?php

//decode by http://www.yunlu99.com/
namespace app\api\validate;

class School extends \think\validate
{
	protected $rule = ["wxapp_id" => ["require"], "s_name" => ["require", "unique:school"], "plat_rate" => [0 => "require", "regex" => "/^[0-9]*\$/"], "school_rate" => [0 => "require", "regex" => "/^[0-9]*\$/"], "second_rate" => [0 => "require", "regex" => "/^[0-9]*\$/"], "edit_status" => ["require"], "robot_key" => ["require"]];
	protected $message = ["wxapp_id.require" => "平台id不能为空", "s_name.require" => "学校名称不能为空", "s_name.unique" => "学校名称已经存在", "plat_rate.require" => "平台抽成不能为空", "plat_rate.regex" => "平台抽成格式错误", "school_rate.require" => "学校抽成不能为空", "school_rate.regex" => "请输入1-100的整数", "second_rate.require" => "二手市场抽成百分比不能为空", "second_rate.regex" => "二手市场抽成百分比格式错误", "edit_status.require" => "是否允许分校修改跑腿抽佣不能为空", "robot_key.require" => "机器人key不能为空"];
	protected $scene = [];
}