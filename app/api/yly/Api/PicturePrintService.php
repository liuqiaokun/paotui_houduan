<?php

//decode by http://www.yunlu99.com/
namespace App\Api;

class PicturePrintService extends RpcService
{
	public function index($machineCode, $pictureUrl, $originId)
	{
		return $this->client->call("pictureprint/index", ["machine_code" => $machineCode, "picture_url" => $pictureUrl, "origin_id" => $originId]);
	}
}