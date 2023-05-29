<?php

//decode by http://www.yunlu99.com/
namespace app\admin\service\Cms;

class CataTreeService
{
	private $cat;
	public function __construct()
	{
		$cat = new \org\Category(["class_id", "pid", "class_name", "class_name"]);
		$this->cat = $cat;
	}
	public function getSubclassId($data, $class_id)
	{
		$data = $this->cat->getTree($data, $class_id);
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
}