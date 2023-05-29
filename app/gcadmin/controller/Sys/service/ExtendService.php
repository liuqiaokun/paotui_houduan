<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys\service;

class ExtendService
{
	public static $fields = [];
	public static $adminActions = [];
	public static $apiActions = [];
	public static function getExtendFieldList($fieldInfo, $type, $applicationInfo, $menuInfo)
	{
		$str = "";
		switch ($fieldInfo["type"]) {
			case 100:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
		}
		return $str;
	}
	public static function getAdminExtendFuns($actionInfo, $fieldList)
	{
		$str = "";
		switch ($actionInfo["type"]) {
			case 100:
				$str .= "\tfunction " . $actionInfo["action_name"] . " (){\n";
				$str .= "\t\treturn 'hello word';\n";
				$str .= "\t}\n";
				break;
		}
		return $str;
	}
	public static function getApiExtendFuns($actionInfo, $fieldList)
	{
		$str = "";
		switch ($actionInfo["type"]) {
			case 100:
				$str .= "\tfunction " . $actionInfo["action_name"] . " (){\n";
				$str .= "\t\treturn 'hello word';\n";
				$str .= "\t}\n";
				break;
		}
		return $str;
	}
}