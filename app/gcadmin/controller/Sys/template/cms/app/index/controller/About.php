<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

class About extends Base
{
	public function index()
	{
		$class_id = $this->request->param("class_id", "", "intval");
		$p = $this->request->param("p", 1, "intval");
		empty($class_id) && $this->error("栏目ID不能为空");
		$classInfo = checkData(\app\ApplicationName\model\Catagory::find($class_id), false);
		empty($classInfo) && $this->error("栏目信息不存在");
		$position = \app\ApplicationName\facade\Cat::getPosition($class_id);
		$topCategoryInfo = \app\ApplicationName\facade\Cat::getTopBigInfo($class_id);
		$this->view->assign("media", \app\ApplicationName\service\BaseService::getMedia($classInfo["class_name"], $classInfo["keyword"], $classInfo["description"]));
		$this->view->assign("classInfo", $classInfo);
		$this->view->assign("class_name", $classInfo["class_name"]);
		$this->view->assign("classid", $classInfo["class_id"]);
		$this->view->assign("pname", $topCategoryInfo["class_name"]);
		$this->view->assign("pid", $topCategoryInfo["class_id"]);
		$this->view->assign("position", $position);
		$this->view->assign("sub_data", \app\ApplicationName\model\Catagory::where("pid", $topCategoryInfo["class_id"])->count());
		$this->view->assign("p", $p);
		if ($classInfo["type"] == 1) {
			$content = \app\ApplicationName\model\Content::where("class_id", $classInfo["class_id"])->find();
			$this->view->assign("info", checkData($content, false));
		}
		$default_themes = config("xhadmin.default_themes") ? config("xhadmin.default_themes") : "index";
		return view($default_themes . "/" . $classInfo["list_tpl"]);
	}
}