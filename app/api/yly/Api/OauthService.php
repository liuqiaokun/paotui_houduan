<?php

//decode by http://www.yunlu99.com/
namespace app\api\yly\Api;

class OauthService extends RpcService
{
	public function setPushUrl($cmd, $url, $status = "open")
	{
		return $this->client->call("oauth/setpushurl", ["cmd" => $cmd, "url" => $url, "status" => $status]);
	}
}