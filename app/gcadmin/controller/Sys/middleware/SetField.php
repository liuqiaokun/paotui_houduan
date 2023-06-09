<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys\middleware;

class SetField extends \app\gcadmin\controller\Admin
{
	public function handle($request, \Closure $next)
	{
		$data = $request->param();
		$field_letter_status = !is_null(config("my.field_letter_status")) ? config("my.field_letter_status") : true;
		if ($field_letter_status) {
			$data["field"] = strtolower(trim($data["field"]));
			if (!preg_match("/^[a-z_|0-9]+\$/", $data["field"])) {
				return json(["status" => "01", "msg" => "字段格式错误"]);
			}
		}
		if ($data["is_field"]) {
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
			$menuInfo = \app\gcadmin\controller\Sys\model\Menu::find($data["menu_id"]);
			$fields = explode("|", $data["field"]);
			$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
			try {
				foreach ($fields as $key => $val) {
					$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "{$menuInfo["table_name"]} ADD {$val} {$datatype}({$maxlen}{$property["decimal"]}) COMMENT '{$data["name"]}' {$default}";
					\think\facade\Db::connect($connect)->execute($sql);
					if (!empty($data["indexdata"])) {
						\think\facade\Db::connect($connect)->execute("ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "{$menuInfo["table_name"]} ADD " . $data["indexdata"] . " (  `" . $val . "` )");
					}
				}
			} catch (\Exception $e) {
				return json(["status" => "01", "msg" => $e->getMessage()]);
			}
		}
		return $next($request);
	}
}