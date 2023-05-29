<?php

//decode by http://www.yunlu99.com/
namespace app\api\yly\Api;

class RpcService
{
	protected $client;
	public function __construct($token, \app\api\yly\Config\YlyConfig $config)
	{
		$this->client = new \app\api\yly\Protocol\YlyRpcClient($token, $config);
	}
}