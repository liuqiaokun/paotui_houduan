<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\service\Cms;

class CatagoryService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = db("catagory")->field($field)->alias("a")->join("menu b", "a.module_id=b.menu_id", "left")->where($where)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page]);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res->items(), "total" => $res->total()];
	}
	public static function add($data)
	{
		if (empty($data["class_name"])) {
			throw new \think\exception\ValidateException("栏目名称不能为空");
		}
		$filepath = self::getFilepath($data["class_name"], $data["class_id"]);
		if (empty($data["filepath"])) {
			$filepath = rtrim(config("xhadmin.filepath"), "/") . "/" . $filepath;
		} else {
			$filepath = $data["filepath"] . "/" . $filepath;
		}
		$data["filepath"] = $filepath;
		try {
			$res = \app\gcadmin\model\Cms\Catagory::create($data);
			if ($res->class_id) {
				\app\gcadmin\model\Cms\Catagory::update(["class_id" => $res->class_id, "sortid" => $res->class_id]);
			}
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->class_id;
	}
	public static function update($data)
	{
		if (empty($data["class_name"])) {
			throw new \think\exception\ValidateException("栏目名称不能为空");
		}
		if (empty($data["filepath"]) || empty($data["filename"])) {
			$data["filename"] = "index.html";
			$data["filepath"] = rtrim(config("xhadmin.filepath"), "/") . "/" . self::getFilepath($data["class_name"], $data["class_id"]);
		}
		try {
			$res = \app\gcadmin\model\Cms\Catagory::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
	public static function setSort($class_id, $type)
	{
		$data = \app\gcadmin\model\Cms\Catagory::find($class_id);
		if ($type == 1) {
			$where["sortid"] = ["<", $data["sortid"]];
			$where["pid"] = $data["pid"];
			$info = \app\gcadmin\model\Cms\Catagory::where(formatWhere($where))->order("sortid desc")->find();
		} else {
			$where["sortid"] = [">", $data["sortid"]];
			$where["pid"] = $data["pid"];
			$info = \app\gcadmin\model\Cms\Catagory::where(formatWhere($where))->order("sortid asc")->find();
		}
		if ($info && $data) {
			\app\gcadmin\model\Cms\Catagory::update(["class_id" => $class_id, "sortid" => $info->sortid]);
			\app\gcadmin\model\Cms\Catagory::update(["class_id" => $info->class_id, "sortid" => $data->sortid]);
		}
	}
	public static function tplList($default_themes = "")
	{
		$tplDir = app()->getRootPath() . "/app/ApplicationName/view/" . $default_themes;
		if (!is_dir($tplDir)) {
			return false;
		}
		$listFile = scandir($tplDir);
		if (is_array($listFile)) {
			$list = [];
			foreach ($listFile as $key => $value) {
				if ($value != "." && $value != "..") {
					$list[$key]["file"] = $value;
					$list[$key]["name"] = substr($value, 0, -5);
				}
			}
		}
		return $list;
	}
	public static function getFilepath($classname, $classId)
	{
		$classname = preg_replace("/\\s+/", "-", $classname);
		$pattern = "/[^\\x{4e00}-\\x{9fa5}\\d\\w\\-]+/u";
		$classname = preg_replace($pattern, "", $classname);
		$filepath = substr(\org\Pinyin::output($classname, true), 0, 30);
		$filepath = trim($filepath, "-");
		$where = [];
		if (!empty($classId)) {
			$where["class_id"] = ["<>", $classId];
		}
		$where["filepath"] = $filepath;
		$info = \app\gcadmin\model\Cms\Catagory::where(formatWhere($where))->find();
		if (empty($info)) {
			return $filepath;
		} else {
			return $filepath . rand(1, 9);
		}
	}
}