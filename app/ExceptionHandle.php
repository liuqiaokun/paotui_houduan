<?php

//decode by http://www.yunlu99.com/
namespace app;

class ExceptionHandle extends \think\exception\Handle
{
	private $error_log_db = true;
	public function render($request, \Throwable $e) : \think\Response
	{
		if ($e instanceof \think\exception\ValidateException) {
			return json(["status" => 411, "msg" => $e->getError()]);
		}
		return parent::render($request, $e);
	}
}