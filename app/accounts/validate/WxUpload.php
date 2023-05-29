<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\validate;

class WxUpload extends \think\validate
{
	protected $rule = ["number" => ["require", "unique:wx_upload"], "version_desc" => ["require"]];
	protected $message = ["number.require" => "版本号不能为空", "number.unique" => "版本号已经存在", "version_desc.require" => "版本描述不能为空"];
	protected $scene = ["add" => ["number", "version_desc"], "update" => ["number", "version_desc"]];
}