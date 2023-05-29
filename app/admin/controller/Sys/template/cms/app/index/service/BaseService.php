<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\service;

class BaseService
{
	public static function getMedia($title = "", $keywords = "", $description = "")
	{
		if (empty($title)) {
			$title = config("xhadmin.site_title");
		} else {
			$title = $title . " - " . config("xhadmin.site_title");
		}
		if (empty($keywords)) {
			$keywords = config("xhadmin.keyword");
		}
		if (empty($description)) {
			$description = config("xhadmin.description");
		}
		return checkData(["title" => $title, "keyword" => $keywords, "description" => $description], false);
	}
	public static function showNext($id, $classid)
	{
		$arr = [];
		$map["content_id"] = ["<", $id];
		$map["class_id"] = $classid;
		$pre = \app\ApplicationName\model\Content::where(formatWhere($map))->field("content_id,title,jumpurl")->order("sortid desc,content_id desc")->find();
		$pre = checkData($pre, false);
		if ($pre) {
			$url = getListUrl($pre);
			$str_a = "<a href=\"" . $url . "\">" . $pre["title"] . "</a>";
		} else {
			$str_a = "没有了";
		}
		$con["content_id"] = [">", $id];
		$con["class_id"] = $classid;
		$next = \app\ApplicationName\model\Content::where(formatWhere($con))->field("content_id,title,jumpurl")->order("sortid desc,content_id desc")->find();
		$pre = checkData($next, false);
		if ($next) {
			$url = getListUrl($next);
			$str_b = "<a href=\"" . $url . "\">" . $next["title"] . "</a>";
		} else {
			$str_b = "没有了";
		}
		array_push($arr, $str_a, $str_b);
		return $arr;
	}
}