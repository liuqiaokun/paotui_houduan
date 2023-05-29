<?php

//decode by http://www.yunlu99.com/
namespace app\api\yly\Api;

class PrinterService extends RpcService
{
	public function addPrinter($machineCode, $mSign, $printName = "", $phone = "")
	{
		$params = ["machine_code" => $machineCode, "msign" => $mSign];
		if (!empty($phone)) {
			$params["phone"] = $phone;
		}
		if (!empty($printName)) {
			$params["print_name"] = $printName;
		}
		return $this->client->call("printer/addprinter", $params);
	}
	public function setVoice($machineCode, $content, $isFile = false, $aid = "")
	{
		$params = ["machine_code" => $machineCode, "content" => $content, "is_file" => $isFile];
		if (!empty($aid)) {
			$params["aid"] = $aid;
		}
		return $this->client->call("printer/setvoice", $params);
	}
	public function deleteVoice($machineCode, $aid)
	{
		return $this->client->call("printer/deletevoice", ["machine_code" => $machineCode, "aid" => $aid]);
	}
	public function deletePrinter($machineCode)
	{
		return $this->client->call("printer/deleteprinter", ["machine_code" => $machineCode]);
	}
	public function shutdownRestart($machineCode, $responseType)
	{
		return $this->client->call("printer/shutdownrestart", ["machine_code" => $machineCode, "response_type" => $responseType]);
	}
	public function setsound($machineCode, $voice, $responseType)
	{
		return $this->client->call("printer/setsound", ["machine_code" => $machineCode, "voice" => $voice, "response_type" => $responseType]);
	}
	public function printInfo($machineCode)
	{
		return $this->client->call("printer/printinfo", ["machine_code" => $machineCode]);
	}
	public function getVersion($machineCode)
	{
		return $this->client->call("printer/getversion", ["machine_code" => $machineCode]);
	}
	public function cancelAll($machineCode)
	{
		return $this->client->call("printer/cancelall", ["machine_code" => $machineCode]);
	}
	public function cancelOne($machineCode, $orderId)
	{
		return $this->client->call("printer/cancelone", ["machine_code" => $machineCode, "order_id" => $orderId]);
	}
	public function setIcon($machineCode, $imgUrl)
	{
		return $this->client->call("printer/seticon", ["machine_code" => $machineCode, "img_url" => $imgUrl]);
	}
	public function deleteIcon($machineCode)
	{
		return $this->client->call("printer/deleteicon", ["machine_code" => $machineCode]);
	}
	public function btnPrint($machineCode, $responseType)
	{
		return $this->client->call("printer/btnprint", ["machine_code" => $machineCode, "response_type" => $responseType]);
	}
	public function getOrder($machineCode, $responseType)
	{
		return $this->client->call("printer/getorder", ["machine_code" => $machineCode, "response_type" => $responseType]);
	}
	public function getOrderStatus($machineCode, $orderId)
	{
		return $this->client->call("printer/getorderstatus", ["machine_code" => $machineCode, "order_id" => $orderId]);
	}
	public function getOrderPagingList($machineCode, $pageIndex = 1, $pageSize = 10)
	{
		return $this->client->call("printer/getorderpaginglist", ["machine_code" => $machineCode, "page_index" => $pageIndex, "page_size" => $pageSize]);
	}
	public function getPrintStatus($machineCode)
	{
		return $this->client->call("printer/getprintstatus", ["machine_code" => $machineCode]);
	}
}