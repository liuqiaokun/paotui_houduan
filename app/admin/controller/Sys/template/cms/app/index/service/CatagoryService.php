<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\service;

class CatagoryService
{
	private $data;
	private $cat;
	public function __construct()
	{
		$cat = new \org\Category(["class_id", "pid", "class_name", "class_name"]);
		$data = \app\ApplicationName\model\Catagory::where(["status" => 1])->field("class_id,pid,class_name")->order("sortid asc,class_id asc")->select();
		$this->data = htmlOutList($data, false);
		$this->cat = $cat;
	}
	public function getSubclassId($class_id)
	{
		$data = $this->cat->getTree($this->data, $class_id);
		if ($data) {
			$list = [];
			foreach ($data as $value) {
				$list[] = $value["class_id"];
			}
			return $class_id . "," . implode(",", $list);
		} else {
			return $class_id;
		}
	}
	public function getPosition($class_id)
	{
		$data = $this->cat->getPath($this->data, $class_id);
		$pos = "当前位置：<a href=\"" . url("ApplicationName/Index/index") . "\">首页</a>";
		foreach ($data as $val) {
			$url = getClassUrl($val);
			$pos .= "&nbsp;&gt;&gt;&nbsp;<a href=\"" . $url . "\">" . $val["class_name"] . "</a>";
		}
		return $pos;
	}
	public function getTopBigInfo($class_id)
	{
		$data = $this->cat->getPath($this->data, $class_id);
		return $data[0];
	}
}