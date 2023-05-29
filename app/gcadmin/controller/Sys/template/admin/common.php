<?php

//decode by http://www.yunlu99.com/
error_reporting(0);
function getControllerName($controller_name)
{
	if ($controller_name && strpos($controller_name, "/") > 0) {
		$controller_name = explode("/", $controller_name)[1];
	}
	return $controller_name;
}
function getUseName($controller_name)
{
	if ($controller_name && strpos($controller_name, "/") > 0) {
		$controller_name = str_replace("/", "\\", $controller_name);
	}
	return $controller_name;
}
function getDbName($controller_name)
{
	if ($controller_name && strpos($controller_name, "/") > 0) {
		$controller_name = "\\" . explode("/", $controller_name)[0];
	} else {
		$controller_name = "";
	}
	return $controller_name;
}
function getViewName($controller_name)
{
	if ($controller_name && strpos($controller_name, "/") > 0) {
		$arr = explode("/", $controller_name);
		$controller_name = ucfirst($arr[0]) . "/" . \think\helper\Str::snake($arr[1]);
	} else {
		$controller_name = \think\helper\Str::snake($controller_name);
	}
	return $controller_name;
}
function getUrlName($controller_name)
{
	if ($controller_name && strpos($controller_name, "/") > 0) {
		$controller_name = str_replace("/", ".", $controller_name);
	}
	return $controller_name;
}
function getClassUrl($class)
{
	if (empty($class["jumpurl"])) {
		$url_type = config("url_type") ? config("url_type") : 1;
		if ($url_type == 1) {
			$url = url("index/About/index", ["class_id" => $class["class_id"]]);
		} else {
			if ($class["filepath"] == "/") {
				$url = "/" . $class["filename"];
			} else {
				$url = $class["filepath"] . "/" . $class["filename"];
			}
		}
	} else {
		$url = $class["jumpurl"];
	}
	return $url;
}
function getListUrl($newslist)
{
	if (!empty($newslist["jumpurl"])) {
		$url = $newslist["jumpurl"];
	} else {
		$url_type = config("xhadmin.url_type") ? config("xhadmin.url_type") : 1;
		if ($url_type == 1) {
			$url = url("index/View/index", ["content_id" => $newslist["content_id"]]);
		} else {
			$info = db("content")->alias("a")->join("catagory b", "a.class_id=b.class_id")->where(["a.content_id" => $newslist["content_id"]])->field("a.content_id,b.filepath")->find();
			$url = $info["filepath"] . "/" . $info["content_id"] . ".html";
		}
	}
	return $url;
}
function getSpic($newslist)
{
	if ($newslist) {
		$targetimages = pathinfo($newslist["pic"]);
		$newpath = $targetimages["dirname"] . "/" . "s_" . $targetimages["basename"];
		return $newpath;
	}
}
function U($classid)
{
	$url_type = config("xhadmin.url_type") ? config("xhadmin.url_type") : 1;
	if ($url_type == 1) {
		$url = url("index/About/index", ["class_id" => $classid]);
	} else {
		$info = db("catagory")->where("class_id", $classid)->find();
		$filepath = $info["filepath"] == "/" ? "" : "/" . trim($info["filepath"], "/");
		$filename = $info["filename"] == "index.html" ? "" : $info["filename"];
		$url = $filepath . "/" . $filename;
	}
	return $url;
}
function killword($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
	if (function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif (function_exists("iconv_substr")) {
		$slice = iconv_substr($str, $start, $length, $charset);
	} else {
		$re["utf-8"] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re["gb2312"] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re["gbk"] = "/[\x01-\x7f]|[\x81-\xfe][@-\xfe]/";
		$re["big5"] = "/[\x01-\x7f]|[\x81-\xfe]([@-~]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice . "..." : $slice;
}
function killhtml($str, $length = 0)
{
	if (is_array($str)) {
		foreach ($str as $k => $v) {
			$data[$k] = killhtml($v, $length);
		}
		return $data;
	}
	if (!empty($length)) {
		$estr = htmlspecialchars(preg_replace("/(&[a-zA-Z]{2,5};)|(\\s)/", "", strip_tags(str_replace("[CHPAGE]", "", $str))));
		if ($length < 0) {
			return $estr;
		}
		return killword($estr, 0, $length);
	}
	return htmlspecialchars(trim(strip_tags($str)));
}