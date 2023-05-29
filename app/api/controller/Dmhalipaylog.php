<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhalipaylog extends Common
{
	public function callback()
	{
	}
	public function pay()
	{
		$pay = new \app\api\model\Dmhalipay();
		$ret = $pay->pay();
		return $this->ajaxReturn($this->successCode, "返回成功", $ret);
	}
}