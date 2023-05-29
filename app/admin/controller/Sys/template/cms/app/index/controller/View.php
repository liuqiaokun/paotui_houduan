<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

class View extends Base
{
	public function index()
	{
		$content_id = $this->request->param("content_id", "", "intval");
		empty($content_id) && $this->error("内容ID不能为空");
		$contentInfo = \app\ApplicationName\model\Content::find($content_id);
		empty($contentInfo["class_id"]) && $this->error("栏目ID不能为空");
		$contentInfo = $contentInfo->toArray();
		$classInfo = checkData(\app\ApplicationName\model\Catagory::find($contentInfo["class_id"]), false);
		!$classInfo && $this->error("栏目信息不存在");
		if ($classInfo["module_id"]) {
			$extInfo = db("menu")->where("menu_id", $classInfo["module_id"])->find();
			$extContentInfo = db($extInfo["table_name"])->where("content_id", $content_id)->find();
			if ($extContentInfo) {
				$contentInfo = array_merge($extContentInfo, $contentInfo);
			}
		}
		$contentInfo = checkData($contentInfo, false);
		$position = \app\ApplicationName\facade\Cat::getPosition($classInfo["class_id"]);
		$topCategoryInfo = \app\ApplicationName\facade\Cat::getTopBigInfo($classInfo["class_id"]);
		$this->view->assign("media", \app\ApplicationName\service\BaseService::getMedia($contentInfo["title"], $contentInfo["keyword"], $contentInfo["description"]));
		$this->view->assign("classInfo", $classInfo);
		$this->view->assign("class_name", $classInfo["class_name"]);
		$this->view->assign("classid", $classInfo["class_id"]);
		$this->view->assign("pname", $topCategoryInfo["class_name"]);
		$this->view->assign("pid", $topCategoryInfo["class_id"]);
		$this->view->assign("position", $position);
		$this->view->assign("info", $contentInfo);
		$this->view->assign("shownext", \app\ApplicationName\service\BaseService::shownext($content_id, $contentInfo["class_id"]));
		$this->view->assign("sub_data", \app\ApplicationName\model\Catagory::where("pid", $topCategoryInfo["class_id"])->count());
		$default_themes = config("xhadmin.default_themes") ? config("xhadmin.default_themes") : "index";
		return view($default_themes . "/" . $classInfo["detail_tpl"]);
	}
	public function hits()
	{
		$content_id = $this->request->param("content_id", "", "intval");
		$data = \app\ApplicationName\model\Content::find($content_id);
		\app\ApplicationName\model\Content::where("content_id", $content_id)->inc("views", 1)->update();
		echo "document.write('" . $data["views"] . "');";
	}
}