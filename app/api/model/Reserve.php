<?php

//decode by http://www.yunlu99.com/
namespace app\api\model;

class Reserve extends \think\Model
{
	use \think\model\concern\SoftDelete;
	protected $deleteTime = "delete_time";
	protected $pk = "id";
	protected $name = "reserve";
}