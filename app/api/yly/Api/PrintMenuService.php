<?php

//decode by http://www.yunlu99.com/
namespace App\Api;

class PrintMenuService extends RpcService
{
	public function addPrintMenu($machineCode, $content)
	{
		return $this->client->call("printmenu/addprintmenu", ["machine_code" => $machineCode, "content" => $content]);
	}
}