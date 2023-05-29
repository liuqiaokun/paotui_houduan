<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys\service;

class ActionService extends \xhadmin\CommonService
{
	public static function saveData($type, $data)
	{
		try {
			$data["name"] = trim($data["name"]);
			$data["block_name"] = trim($data["block_name"]);
			$rule = ["name" => ["regex" => "/^[\\x{4e00}-\\x{9fa5}_a-zA-Z0-9|\\(|\\)|\\（|\\）]+\$/u"], "action_name" => ["regex" => "/^[a-zA-Z_]+\$/"], "pagesize" => ["regex" => "/^[1-9]\\d*\$/"], "tree_config" => ["regex" => "/^[,a-z_]+\$/"], "relate_table" => ["regex" => "/^[a-zA-Z0-9_]+\$/"]];
			$msg = ["name.require" => "操作名称必填", "name.regex" => "操作名称中文数字小写字母或下划线", "action_name.require" => "方法名称必填", "action_name.regex" => "方法名称小写字母组合", "pagesize.regex" => "分页整数", "tree_config.regex" => "树配置小写字母 逗号组合", "relate_table.regex" => "关联表小写字母组合"];
			$validate = \think\facade\Validate::rule($rule)->message($msg);
			if (!$validate->check($data)) {
				throw new ValidateException($validate->getError());
			}
			if ($type == "add") {
				$info = \app\gcadmin\controller\Sys\model\Action::where(["menu_id" => $data["menu_id"], "type" => $data["type"], "action_name" => $data["action_name"]])->find();
				if (!$info) {
					$reset = \app\gcadmin\controller\Sys\model\Action::create($data);
					if ($reset->id) {
						\app\gcadmin\controller\Sys\model\Action::update(["id" => $reset->id, "sortid" => $reset->id]);
						if ($data["type"] == 32) {
							$hszActionList = [];
							$hszActionList[0]["menu_id"] = $data["menu_id"];
							$hszActionList[0]["name"] = "恢复数据";
							$hszActionList[0]["action_name"] = "resumeData";
							$hszActionList[0]["type"] = 34;
							$hszActionList[0]["button_status"] = 1;
							$hszActionList[0]["is_view"] = 1;
							$hszActionList[0]["bs_icon"] = "fa fa-edit";
							$hszActionList[0]["lable_color"] = "success";
							$hszActionList[1]["menu_id"] = $data["menu_id"];
							$hszActionList[1]["name"] = "删除";
							$hszActionList[1]["action_name"] = "trashDelete";
							$hszActionList[1]["type"] = 33;
							$hszActionList[1]["button_status"] = 1;
							$hszActionList[1]["is_view"] = 1;
							$hszActionList[1]["bs_icon"] = "fa fa-trash";
							$hszActionList[1]["lable_color"] = "danger";
							foreach ($hszActionList as $k => $v) {
								$res = db("action")->insertGetId($v);
								db("action")->update(["id" => $res, "sortid" => $res]);
							}
						}
						if ($data["type"] == 31) {
							$menuInfo = db("menu")->where("menu_id", $data["menu_id"])->find();
							$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
							$delete_field = !is_null(config("my.delete_field")) ? config("my.delete_field") : "delete_time";
							$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "{$menuInfo["table_name"]} ADD {$delete_field} int(10) COMMENT '软删除标记' DEFAULT null";
							\think\facade\Db::connect($connect)->execute($sql);
						}
					}
				} else {
					throw new \Exception("方法已经存在");
				}
			} elseif ($type == "edit") {
				$reset = \app\gcadmin\controller\Sys\model\Action::update($data);
				$actionInfo = db("action")->where("id", $data["id"])->find();
				$menuInfo = db("menu")->where("menu_id", $actionInfo["menu_id"])->find();
				$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
				if ($data["type"] == 31) {
					$delete_field = !is_null(config("my.delete_field")) ? config("my.delete_field") : "delete_time";
					$deleteFieldStatus = self::getFieldStatus(config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"], $delete_field, $connect);
					if (!$deleteFieldStatus) {
						$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "{$menuInfo["table_name"]} ADD {$delete_field} int(10) COMMENT '软删除标记' DEFAULT null";
						\think\facade\Db::connect($connect)->execute($sql);
					}
				}
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return $reset;
	}
	public static function addFast($data)
	{
		$actions = explode(",", $data["actions"]);
		$postField = \app\gcadmin\controller\Sys\model\Field::where(["is_post" => 1, "menu_id" => $data["menu_id"]])->column("field");
		if ($actions) {
			try {
				foreach ($actions as $key => $val) {
					$actionInfo = explode("|", $val);
					$dt["menu_id"] = $data["menu_id"];
					$dt["name"] = $actionInfo[0];
					$dt["action_name"] = $actionInfo[1];
					$dt["type"] = $actionInfo[2];
					$dt["bs_icon"] = $actionInfo[3];
					$dt["is_view"] = 1;
					$dt["block_name"] = $actionInfo[0];
					if (in_array($actionInfo[2], [4, 5])) {
						$dt["button_status"] = 1;
					} else {
						$dt["button_status"] = 0;
					}
					if (in_array($actionInfo[2], [3, 4, 15])) {
						$dt["fields"] = implode(",", $postField);
						$len = count($postField);
						if ($len <= 3) {
							$width = 600;
							$height = $len * 50 + 300;
							$size = $width . "px|" . $height . "px";
						} else {
							if ($len > 3 && $len <= 8) {
								$width = 800;
								$height = $len * 50 + 200;
								$size = $width . "px|" . $height . "px";
							} else {
								if ($len > 8) {
									$width = "800";
									$size = $width . "px|100%";
								}
							}
						}
						$dt["remark"] = $size;
					} else {
						$dt["remark"] = "";
						$dt["fields"] = "";
					}
					switch ($actionInfo[2]) {
						case 3:
							$label_color = "primary";
							break;
						case 4:
							$label_color = "success";
							break;
						case 5:
							$label_color = "danger";
							break;
						case 15:
							$label_color = "info";
							break;
						case 12:
							$label_color = "warning";
							break;
						case 13:
							$label_color = "warning";
							break;
					}
					$dt["lable_color"] = $label_color;
					$info = \app\gcadmin\controller\Sys\model\Action::where(["menu_id" => $data["menu_id"], "action_name" => $actionInfo[1]])->find();
					if (!$info) {
						self::saveData("add", $dt);
					} else {
						throw new \Exception("方法已经存在");
					}
				}
			} catch (\Exception $e) {
				throw new \Exception($e->getMessage());
			}
			return true;
		}
	}
	public static function arrowsort($id, $type)
	{
		$data = \app\gcadmin\controller\Sys\model\Action::find($id);
		if ($type == 1) {
			$map = "sortid < " . $data["sortid"] . " and menu_id = " . $data["menu_id"];
			$info = \app\gcadmin\controller\Sys\model\Action::where($map)->order("sortid desc")->find();
		} else {
			$map = "sortid > " . $data["sortid"] . " and menu_id = " . $data["menu_id"];
			$info = \app\gcadmin\controller\Sys\model\Action::where($map)->order("sortid asc")->find();
		}
		try {
			if ($info && $data) {
				\app\gcadmin\controller\Sys\model\Action::update(["id" => $id, "sortid" => $info["sortid"]]);
				\app\gcadmin\controller\Sys\model\Action::update(["id" => $info["id"], "sortid" => $data["sortid"]]);
			} else {
				throw new \Exception("目标位置没有数据");
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return true;
	}
	public static function getFieldStatus($tablename, $field, $connect)
	{
		$list = \think\facade\Db::connect($connect)->query("show columns from " . $tablename);
		foreach ($list as $v) {
			$arr[] = $v["Field"];
		}
		if (in_array($field, $arr)) {
			return true;
		}
	}
}