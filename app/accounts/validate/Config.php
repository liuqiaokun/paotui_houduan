<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\validate;

class Config extends \think\validate
{
	protected $rule = ["site_title" => ["require"], "sub_site_title" => ["require"]];
	protected $message = ["site_title.require" => "站点名称不能为空", "sub_site_title.require" => "子站点名称不能为空"];
	protected $scene = [];
}