<?php

//decode by http://www.yunlu99.com/
namespace app\api\yly\Api;

class ExpressPrintService extends RpcService
{
	public function index($machineCode, $content, $originId, $sandbox = 0)
	{
		return $this->client->call("expressprint/index", ["machine_code" => $machineCode, "content" => $content, "origin_id" => $originId, "sandbox" => $sandbox]);
	}
}