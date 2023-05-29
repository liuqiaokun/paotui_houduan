<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Sys\service;

class FieldService extends \xhadmin\CommonService
{
	public static function saveData($type, $data)
	{
		try {
			$rule = ["name" => "require", "field" => "require", "type" => "require"];
			$msg = ["name.require" => "字段名称必填", "field.require" => "字段必填", "type.require" => "字段类型必填"];
			$validate = \think\facade\Validate::rule($rule)->message($msg);
			if (!$validate->check($data)) {
				throw new ValidateException($validate->getError());
			}
			$field_letter_status = !is_null(config("my.field_letter_status")) ? config("my.field_letter_status") : true;
			if ($field_letter_status) {
				$data["field"] = strtolower(trim($data["field"]));
			}
			if ($type == "add") {
				$info = \app\admin\controller\Sys\model\Field::where(["menu_id" => $data["menu_id"], "field" => $data["field"]])->find();
				$reset = \app\admin\controller\Sys\model\Field::create($data);
				if ($reset->id) {
					\app\admin\controller\Sys\model\Field::update(["id" => $reset->id, "sortid" => $reset->id]);
				}
				if ($data["type"] == 22) {
					$arrinfo = db("action")->where("type", 30)->where("menu_id", $data["menu_id"])->find();
					if (!$arrinfo) {
						$dt["menu_id"] = $data["menu_id"];
						$dt["type"] = 30;
						$dt["name"] = "箭头排序";
						$dt["action_name"] = "arrowsort";
						$pk = db("action")->insertGetId($dt);
						if ($pk) {
							db("action")->where("id", $pk)->update(["sortid" => $pk]);
						}
					}
				}
				$actionList = db("action")->field("id,type,fields")->where("menu_id", $data["menu_id"])->where("type", "in", [3, 4, 15])->select()->toArray();
				if ($actionList && $data["is_field"]) {
					foreach ($actionList as $k => $v) {
						$param["fields"] = $v["fields"] . "," . $data["field"];
						$fieldcount = count(explode(",", $param["fields"]));
						if ($fieldcount <= 3) {
							$width = "600px";
							$height = $fieldcount * 50 + 300 . "px";
						} else {
							if ($fieldcount > 3 && $fieldcount <= 8) {
								$width = "800px";
								$height = $fieldcount * 50 + 200 . "px";
							} else {
								$width = "800px";
								$height = "100%";
							}
						}
						$param["remark"] = $width . "|" . $height;
						db("action")->where("id", $v["id"])->update($param);
					}
				}
			} elseif ($type == "edit") {
				$res = \app\admin\controller\Sys\model\Field::update($data);
				if ($res) {
					$fieldInfo = \app\admin\controller\Sys\model\Field::find($data["id"]);
					if ($data["name"] == "编号" && $data["field"] != $fieldInfo["field"]) {
						\app\admin\controller\Sys\model\Menu::update(["pk_id" => $data["field"], "menu_id" => $fieldInfo["menu_id"]]);
					}
				}
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return $reset;
	}
	public static function arrowsort($id, $type)
	{
		$data = \app\admin\controller\Sys\model\Field::find($id);
		if ($type == 1) {
			$map = "sortid < " . $data["sortid"] . " and menu_id = " . $data["menu_id"];
			$info = \app\admin\controller\Sys\model\Field::where($map)->order("sortid desc")->find();
		} else {
			$map = "sortid > " . $data["sortid"] . " and menu_id = " . $data["menu_id"];
			$info = \app\admin\controller\Sys\model\Field::where($map)->order("sortid asc")->find();
		}
		try {
			if ($info && $data) {
				\app\admin\controller\Sys\model\Field::update(["id" => $id, "sortid" => $info["sortid"]]);
				\app\admin\controller\Sys\model\Field::update(["id" => $info["id"], "sortid" => $data["sortid"]]);
			} else {
				throw new \Exception("目标位置没有数据");
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return true;
	}
}