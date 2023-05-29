<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Cms;

class DoHtml extends \app\admin\controller\Admin
{
	public function index()
	{
		$this->view->assign("tpList", \app\admin\service\Cms\CatagoryService::tplList(config("xhadmin.default_themes")));
		return view("cms/do_html/info");
	}
	public function doIndex()
	{
		if ($this->request->isPost()) {
			config(["url_type" => 2], "xhadmin");
			$index_tpl = input("param.index_tpl", "", "strval");
			!$index_tpl && $this->error("首页模板不能为空");
			$this->view->assign("media", \app\index\service\BaseService::getMedia());
			try {
				$index_name = config("xhadmin.index_name") ? config("xhadmin.index_name") : "index.html";
				$this->filePutContents("./" . $index_name, $index_tpl);
			} catch (\Exception $e) {
				$this->error($e->getMessage());
			}
			return json(["status" => "00", "msg" => "生成成功"]);
		}
	}
	public function doList()
	{
		config(["url_type" => 2], "xhadmin");
		$classId = input("param.classId", "", "intval");
		if (!$this->request->isPost()) {
			$this->view->assign("classId", $classId);
			return view("cms/do_html/listprocess");
		} else {
			if (!$classId) {
				$page = input("param.page", "", "intval");
				$info = db("Catagory")->limit($page - 1, 1)->where("list_tpl", "<>", "")->order("sortid asc")->select()->toArray();
				$info = current($info);
				$count = db("catagory")->where($where)->count();
				if ($info) {
					try {
						$this->getListContent($info);
						$dt["percent"] = ceil($page / $count * 100);
						$dt["filename"] = $info["filepath"] . "/" . $info["filename"];
						return json(["error" => "00", "data" => $dt]);
					} catch (\Exception $e) {
						exit($this->error($e->getMessage()));
					}
				} else {
					return json(["error" => "10"]);
				}
			} else {
				$info = db("catagory")->where("class_id", $classId)->find();
				$count = 1;
				$page = 1;
				if ($info) {
					try {
						$this->getListContent($info);
						$dt["percent"] = ceil($page / $count * 100);
						$dt["filename"] = $info["filepath"] . "/" . $info["filename"];
						return json(["error" => "00", "data" => $dt]);
					} catch (\Exception $e) {
						exit($this->error($e->getMessage()));
					}
				}
			}
		}
	}
	private function getListContent($info)
	{
		config(["url_type" => 2], "xhadmin");
		try {
			$class_id = $info["class_id"];
			$position = $this->getPos($class_id);
			$topCategoryInfo = \app\index\facade\Cat::getTopBigInfo($class_id);
			$this->view->assign("media", \app\index\service\BaseService::getMedia($info["class_name"], $info["keyword"], $info["description"]));
			$this->view->assign("info", $info);
			$this->view->assign("class_name", $info["class_name"]);
			$this->view->assign("classid", $info["class_id"]);
			$this->view->assign("pname", $topCategoryInfo["class_name"]);
			$this->view->assign("pid", $topCategoryInfo["class_id"]);
			$this->view->assign("position", $position);
			$this->view->assign("sub_data", db("catagory")->where(["pid" => $topCategoryInfo["class_id"]])->count());
			$this->view->assign("p", 1);
			if ($info["type"] == 1) {
				$this->view->assign("info", checkData(db("content")->where(["class_id" => $class_id])->find(), false));
			}
			$filepath = "./" . $info["filepath"] . "/" . $info["filename"];
			$this->filePutContents($filepath, $info["list_tpl"]);
			if ($info["type"] == 2) {
				$idx = \app\index\facade\Cat::getSubClassId($info["class_id"]);
				$contentWhere["class_id"] = explode(",", $idx);
				$contentWhere["status"] = 1;
				$contentCount = db("content")->where($contentWhere)->count();
				if ($contentCount > 0) {
					$pagesize = $this->getListPageSize($info);
					!$pagesize && ($pagesize = 10);
					$totalpage = ceil($contentCount / $pagesize);
					if ($totalpage > 1) {
						for ($i = 2; $i <= $totalpage; $i++) {
							$this->view->assign("p", $i);
							$filepath = "./" . $info["filepath"] . "/" . $i . "/" . $info["filename"];
							$this->filePutContents($filepath, $info["list_tpl"]);
						}
					}
				}
			}
			$position = "";
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}
	public function doView()
	{
		config(["url_type" => 2], "xhadmin");
		$classId = input("param.classId", "", "intval");
		$startId = input("param.startId", "", "intval");
		$endId = input("param.endId", "", "intval");
		$pagesize = input("param.pagesize", "", "intval");
		if (!$this->request->isPost()) {
			$this->view->assign("classId", $classId);
			$this->view->assign("pagesize", $pagesize);
			$this->view->assign("startId", $startId);
			$this->view->assign("endId", $endId);
			return view("cms/do_html/viewprocess");
		} else {
			try {
				$page = input("param.page", 1, "intval");
				$classId && ($where["a.class_id"] = $classId);
				$startId && ($where["a.content_id"] = [">=", $startId]);
				$endId && ($where["a.content_id"] = ["<=", $endId]);
				$where["b.type"] = 2;
				$start = ($page - 1) * $pagesize;
				$contentList = db("content")->alias("a")->join("catagory b", "a.class_id=b.class_id")->where(formatWhere($where))->field("a.*,b.class_name,b.type,b.list_tpl,b.detail_tpl,b.jumpurl,b.module_id,b.filepath")->limit($start, $pagesize)->order("content_id asc")->select()->toArray();
				$count = db("content")->alias("a")->join("catagory b", "a.class_id=b.class_id")->where(formatWhere($where))->limit($start, $pagesize)->order("content_id asc")->count();
				$per = ceil($count / $pagesize);
				if ($contentList) {
					foreach ($contentList as $key => $val) {
						if ($val["type"] == 2 && !empty($val["list_tpl"]) || !empty($val["detail_tpl"]) && !empty($val["jumpurl"])) {
							$topCategoryInfo = \app\index\facade\Cat::getTopBigInfo($val["class_id"]);
							if ($val["module_id"]) {
								$extendInfo = db("menu")->where("menu_id", $val["module_id"])->find();
								$extInfo = db($extendInfo["table_name"])->where("content_id", $val["content_id"])->find();
								if ($extInfo) {
									unset($extInfo["data_id"]);
									unset($extInfo["content_id"]);
									$val = array_merge($val, $extInfo);
								}
							}
							$val = checkData($val, false);
							$this->view->assign("media", \app\index\service\BaseService::getMedia($val["title"], $val["keyword"], $val["description"]));
							$this->view->assign("classInfo", $val);
							$this->view->assign("class_name", $val["class_name"]);
							$this->view->assign("classid", $val["class_id"]);
							$this->view->assign("pname", $topCategoryInfo["class_name"]);
							$this->view->assign("pid", $topCategoryInfo["class_id"]);
							$this->view->assign("position", $this->getPos($val["class_id"]));
							$this->view->assign("info", $val);
							$this->view->assign("shownext", \app\index\service\BaseService::shownext($val["content_id"], $val["class_id"]));
							$this->view->assign("sub_data", db("catagory")->where(["pid" => $topCategoryInfo["class_id"]])->count());
							$filepath = "./" . $val["filepath"] . "/" . $val["content_id"] . ".html";
							$this->filePutContents($filepath, $val["detail_tpl"]);
						}
					}
					$dt["filename"] = $filepath;
					$dt["percent"] = ceil($page / $per * 100);
					return json(["error" => "00", "data" => $dt]);
				} else {
					return json(["error" => "10"]);
				}
			} catch (\Exception $e) {
				throw new \Exception($e->getMessage());
			}
		}
	}
	private function getPos($classId)
	{
		$info = db("catagory")->where("class_id", $classId)->find();
		$parentInfo = db("catagory")->where("class_id", $info["pid"])->find();
		$topBigInfo = db("catagory")->where("class_id", $parentInfo["pid"])->find();
		$pos = "当前位置：<a href=\"" . url("@index") . "\">首页</a>&nbsp;&gt;&gt;&nbsp;";
		if ($topBigInfo) {
			$pos .= "<a href=\"" . U($topBigInfo["class_id"]) . "\">" . $topBigInfo["class_name"] . "</a>&nbsp;&gt;&gt;&nbsp;<a href=\"" . U($parentInfo["class_id"]) . "\">" . $parentInfo["class_name"] . "</a>&nbsp;&gt;&gt;&nbsp;<a href=\"" . U($info["class_id"]) . "\">" . $info["class_name"] . "</a>";
		} else {
			if ($parentInfo) {
				$pos .= "<a href=\"" . U($parentInfo["class_id"]) . "\">" . $parentInfo["class_name"] . "</a>" . "&nbsp;&gt;&gt;&nbsp;<a href=\"" . U($info["class_id"]) . "\">" . $info["class_name"] . "</a>";
			} else {
				$pos .= "<a href=\"" . U($info["class_id"]) . "\">" . $info["class_name"] . "</a>";
			}
		}
		return $pos;
	}
	private function filePutContents($filepath, $tpl)
	{
		ob_start();
		$default_themes = config("xhadmin.default_themes") ? config("xhadmin.default_themes") : "index";
		$content = $this->view->fetch("index@" . $default_themes . "/" . $tpl);
		echo $content;
		$_cache = ob_get_contents();
		ob_end_clean();
		if ($_cache) {
			$File = new \think\template\driver\File();
			$File->write($filepath, $_cache);
		}
	}
	private function getListPageSize($info)
	{
		$default_themes = config("xhadmin.default_themes") ? config("xhadmin.default_themes") : "index";
		$tpl = $info["list_tpl"];
		$content = file_get_contents(app()->getRootPath() . "app/index/view/" . $default_themes . "/" . $tpl . ".html");
		if ($content) {
			preg_match_all("/\\{list(.*)num=['\\\"](\\d+)['\\\"](.*)\\}/", $content, $res);
			return $res[2][0];
		}
	}
}