<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\model;

class Order extends \think\Model
{
	use \think\model\concern\SoftDelete;
	protected $deleteTime = "delete_time";
	protected $pk = "id";
	protected $name = "dmh_school_order";
}