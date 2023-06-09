<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys\middleware;

class UpField extends \app\gcadmin\controller\Admin
{
	public function handle($request, \Closure $next)
	{
		$data = $request->param();
		$field_letter_status = !is_null(config("my.field_letter_status")) ? config("my.field_letter_status") : true;
		if ($field_letter_status) {
			$data["field"] = strtolower(trim($data["field"]));
			$data["pk_id"] = strtolower(trim($data["pk_id"]));
			if (!preg_match("/^[a-z_|0-9]+\$/", $data["field"])) {
				return json(["status" => "01", "msg" => "字段格式错误"]);
			}
		}
		$fieldInfo = \app\gcadmin\controller\Sys\model\Field::find($data["id"]);
		if ($data["is_field"] && $fieldInfo["menu_id"] != config("my.config_module_id")) {
			$typeField = \app\gcadmin\controller\Sys\service\FieldSetService::typeField() + \app\gcadmin\controller\Sys\service\ExtendService::$fields;
			$propertyField = \app\gcadmin\controller\Sys\service\FieldSetService::propertyField();
			$typeData = $typeField[$data["type"]];
			$property = $propertyField[$typeData["property"]];
			$property["decimal"] = !empty($property["decimal"]) ? "," . $property["decimal"] : "";
			$maxlen = !empty($data["length"]) ? $data["length"] : $property["maxlen"];
			$datatype = !empty($data["datatype"]) ? $data["datatype"] : $property["name"];
			if ((!empty($data["default_value"]) || is_numeric($data["default_value"])) && !in_array($data["type"], [7, 31, 12, 21, 25, 17, 32])) {
				if ($data["type"] == 13) {
					$data["default_value"] = "0";
				}
				$default = "DEFAULT '" . $data["default_value"] . "'";
			} else {
				$default = "DEFAULT NULL";
			}
			$menuInfo = \app\gcadmin\controller\Sys\model\Menu::find($fieldInfo["menu_id"]);
			$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
			if ($data["name"] == "编号") {
				$auto = "AUTO_INCREMENT";
				$maxlen = !empty($data["length"]) ? $data["length"] : 11;
				$datatype = !empty($data["datatype"]) ? $data["datatype"] : "int";
				$default = "NOT NULL";
				$primary_key = "PRIMARY KEY";
			} else {
				$auto = "";
			}
			$fields = explode("|", $data["field"]);
			$tableFileds = explode("|", $fieldInfo["field"]);
			try {
				foreach ($fields as $key => $val) {
					if (self::getFieldStatus(config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"], $tableFileds[$key], $connect)) {
						$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "{$menuInfo["table_name"]} CHANGE {$tableFileds[$key]} {$fields[$key]} {$datatype}({$maxlen}{$property["decimal"]}) COMMENT '{$data["name"]}' {$default} {$auto}";
						\think\facade\Db::connect($connect)->execute($sql);
					} else {
						$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "{$menuInfo["table_name"]} ADD {$val} {$datatype}({$maxlen}{$property["decimal"]}) COMMENT '{$data["name"]}' {$default} {$auto} {$primary_key}";
						\think\facade\Db::connect($connect)->execute($sql);
					}
					$status = self::getTableIndex(config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"], $fields[$key], $connect);
					if (!empty($data["indexdata"]) && !$status) {
						\think\facade\Db::connect($connect)->execute("ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "{$menuInfo["table_name"]} ADD " . $data["indexdata"] . " (  `" . $val . "` )");
					}
				}
				if ($data["name"] == "编号" && $data["field"] != $menuInfo["pk_id"]) {
					db("menu")->where("menu_id", $fieldInfo["menu_id"])->update(["pk_id" => $data["field"]]);
				}
			} catch (\Exception $e) {
				return json(["status" => "01", "msg" => $e->getMessage()]);
			}
		}
		return $next($request);
	}
	public static function getTableIndex($tablename, $indexName, $connect)
	{
		$list = \think\facade\Db::connect($connect)->query("show index from " . $tablename);
		foreach ($list as $k => $v) {
			if ($v["Column_name"] == $indexName) {
				$status = true;
			}
		}
		return $status;
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