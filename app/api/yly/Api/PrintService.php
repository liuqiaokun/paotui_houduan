<?php

//decode by http://www.yunlu99.com/
namespace app\api\yly\Api;

class PrintService extends RpcService
{
	public function index($machineCode, $content, $originId)
	{
		return $this->client->call("print/index", ["machine_code" => $machineCode, "content" => $content, "origin_id" => $originId]);
	}
}