<?php

//decode by http://www.yunlu99.com/
header("Content-type: text/html; charset=utf-8");
include "HttpClient.class.php";
define("USER", "");
define("UKEY", "");
define("SN", "");
define("IP", "api.feieyun.cn");
define("PORT", 80);
define("PATH", "/Api/Open/");
$content = "<CB>肯德基</CB><BR>";
$content .= "名称　　　　　 单价  数量 金额<BR>";
$content .= "--------------------------------<BR>";
$content .= "饭　　　　　 　10.0   10  100.0<BR>";
$content .= "炒饭　　　　　 10.0   10  100.0<BR>";
$content .= "蛋炒饭　　　　 10.0   10  100.0<BR>";
$content .= "鸡蛋炒饭　　　 10.0   10  100.0<BR>";
$content .= "西红柿炒饭　　 10.0   10  100.0<BR>";
$content .= "西红柿蛋炒饭　 10.0   10  100.0<BR>";
$content .= "西红柿鸡蛋炒饭 10.0   10  100.0<BR>";
$content .= "--------------------------------<BR>";
$content .= "备注：加辣<BR>";
$content .= "合计：30.0元<BR>";
$content .= "送货地点：广州市南沙区xx路xx号<BR>";
$content .= "联系电话：13888888888888<BR>";
$content .= "订餐时间：2014-08-08 08:08:08<BR>";
$content .= "<QR>http://www.gcnet.vip</QR>";
$content = "<DIRECTION>1</DIRECTION>";
$content .= "<TEXT x='9' y='10' font='12' w='1' h='2' r='0'>#001       五号桌      1/3</TEXT><TEXT x='80' y='80' font='12' w='2' h='2' r='0'>可乐鸡翅</TEXT><TEXT x='9' y='180' font='12' w='1' h='1' r='0'>张三先生       13800138000</TEXT>";
function printerAddlist($printerContent)
{
	$time = time();
	$msgInfo = ["user" => USER, "stime" => $time, "sig" => signature($time), "apiname" => "Open_printerAddlist", "printerContent" => $printerContent];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
		echo "error";
	} else {
		$result = $client->getContent();
		echo $result;
	}
}
function printMsg($store, $content, $times)
{
	$time = time();
	$msgInfo = ["user" => $store["printer_user"], "stime" => $time, "sig" => signature($store, $time), "apiname" => "Open_printMsg", "sn" => $store["printer"], "content" => $content, "times" => $times];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
	} else {
		$result = $client->getContent();
	}
}
function printLabelMsg($sn, $content, $times)
{
	$time = time();
	$msgInfo = ["user" => USER, "stime" => $time, "sig" => signature($time), "apiname" => "Open_printLabelMsg", "sn" => $sn, "content" => $content, "times" => $times];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
		echo "error";
	} else {
		$result = $client->getContent();
		echo $result;
	}
}
function printerDelList($snlist)
{
	$time = time();
	$msgInfo = ["user" => USER, "stime" => $time, "sig" => signature($time), "apiname" => "Open_printerDelList", "snlist" => $snlist];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
		echo "error";
	} else {
		$result = $client->getContent();
		echo $result;
	}
}
function printerEdit($sn, $name, $phonenum)
{
	$time = time();
	$msgInfo = ["user" => USER, "stime" => $time, "sig" => signature($time), "apiname" => "Open_printerEdit", "sn" => $sn, "name" => $name, "phonenum" => $phonenum];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
		echo "error";
	} else {
		$result = $client->getContent();
		echo $result;
	}
}
function delPrinterSqs($sn)
{
	$time = time();
	$msgInfo = ["user" => USER, "stime" => $time, "sig" => signature($time), "apiname" => "Open_delPrinterSqs", "sn" => $sn];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
		echo "error";
	} else {
		$result = $client->getContent();
		echo $result;
	}
}
function queryOrderState($orderid)
{
	$time = time();
	$msgInfo = ["user" => USER, "stime" => $time, "sig" => signature($time), "apiname" => "Open_queryOrderState", "orderid" => $orderid];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
		echo "error";
	} else {
		$result = $client->getContent();
		echo $result;
	}
}
function queryOrderInfoByDate($sn, $date)
{
	$time = time();
	$msgInfo = ["user" => USER, "stime" => $time, "sig" => signature($time), "apiname" => "Open_queryOrderInfoByDate", "sn" => $sn, "date" => $date];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
		echo "error";
	} else {
		$result = $client->getContent();
		echo $result;
	}
}
function queryPrinterStatus($sn)
{
	$time = time();
	$msgInfo = ["user" => USER, "stime" => $time, "sig" => signature($time), "apiname" => "Open_queryPrinterStatus", "sn" => $sn];
	$client = new HttpClient(IP, PORT);
	if (!$client->post(PATH, $msgInfo)) {
		echo "error";
	} else {
		$result = $client->getContent();
		echo $result;
	}
}
function signature($store, $time)
{
	return sha1($store["printer_user"] . $store["printer_ukey"] . $time);
}