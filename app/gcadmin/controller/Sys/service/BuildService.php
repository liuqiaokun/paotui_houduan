<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys\service;

class BuildService extends \xhadmin\CommonService
{
	public static function createTimeSearch($val)
	{
		$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-3\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t<div class=\"input-group\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t<div class=\"input-group-btn\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<button data-toggle=\"dropdown\" class=\"btn btn-white dropdown-toggle\" type=\"button\">" . $val["name"] . "范围</button>\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" placeholder=\"时间范围\" class=\"form-control\" id=\"" . $val["field"] . "\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
		return $htmlstr;
	}
	public static function createNumSearch($val)
	{
		$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-2\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t<div class=\"input-group\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t<div class=\"input-group-btn\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<button data-toggle=\"dropdown\" class=\"btn btn-white dropdown-toggle\" type=\"button\">" . $val["name"] . "开始</button>\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" placeholder=\"起始" . $val["name"] . "\" class=\"form-control layer-date\" id=\"" . $val["field"] . "_start\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-2\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t<div class=\"input-group\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t<div class=\"input-group-btn\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<button data-toggle=\"dropdown\" class=\"btn btn-white dropdown-toggle\" type=\"button\">" . $val["name"] . "结束</button>\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" placeholder=\"结束" . $val["name"] . "\" class=\"form-control\" id=\"" . $val["field"] . "_end\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
		return $htmlstr;
	}
	public static function createDistaitSearch($val)
	{
		$htmlstr .= "\t\t\t\t\t\t\t<div class=\"distpicker5\">\n";
		foreach (explode("|", $val["field"]) as $m => $n) {
			if ($m == "0") {
				$areaTitle = "省";
			} elseif ($m == "1") {
				$areaTitle = "市";
			} elseif ($m == "2") {
				$areaTitle = "区";
			}
			$htmlstr .= "\t\t\t\t\t\t\t\t<div class=\"col-sm-2\">\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t\t<div class=\"input-group\">\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<div class=\"input-group-btn\">\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t\t\t\t<button data-toggle=\"dropdown\" class=\"btn btn-white dropdown-toggle\" type=\"button\">" . $areaTitle . "</button>\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t\t\t</div>\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<select lay-ignore id=\"" . $n . "\" class=\"form-control\" ></select>\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t\t</div>\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t</div>\n";
		}
		$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t<script src=\"__PUBLIC__/static/js/plugins/shengshiqu/distpicker.data.js\"></script>\n";
		$htmlstr .= "\t\t\t\t\t\t\t<script src=\"__PUBLIC__/static/js/plugins/shengshiqu/distpicker.js\"></script>\n";
		$htmlstr .= "\t\t\t\t\t\t\t<script src=\"__PUBLIC__/static/js/plugins/shengshiqu/main.js\"></script>\n";
		return $htmlstr;
	}
	public static function createNormaiSearch($v)
	{
		$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-2\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t<div class=\"input-group\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t<div class=\"input-group-btn\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<button data-toggle=\"dropdown\" class=\"btn btn-white dropdown-toggle\" type=\"button\">" . $v["name"] . "</button>\n";
		$htmlstr .= "\t\t\t\t\t\t\t\t\t</div>\n";
		if (in_array($v["type"], [1, 6, 20, 21, 28, 30])) {
			if ($v["field"] == "name") {
				$v["field"] = "name_s";
			}
			$htmlstr .= "\t\t\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" class=\"form-control\" id=\"" . $v["field"] . "\" placeholder=\"" . $v["name"] . "\" />\n";
		}
		if (in_array($v["type"], [2, 3, 4, 23, 27, 29])) {
			if ($v["type"] == 29) {
				$htmlstr .= "\t\t\t\t\t\t\t\t\t<select class=\"form-control chosen\" id=\"" . $v["field"] . "\">\n";
			} else {
				$htmlstr .= "\t\t\t\t\t\t\t\t\t<select class=\"form-control\" id=\"" . $v["field"] . "\">\n";
			}
			$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<option value=\"\">请选择</option>\n";
			if (empty($v["sql"])) {
				$searchArr = explode(",", $v["config"]);
				if ($searchArr) {
					foreach ($searchArr as $k => $v) {
						$valArr = explode("|", $v);
						$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<option value=\"" . $valArr[1] . "\">" . $valArr[0] . "</option>\n";
					}
				}
			} else {
				$menuInfo = db("menu")->where("menu_id", $v["menu_id"])->find();
				$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
				if ($connect) {
					$htmlstr .= "\t\t\t\t\t\t\t\t\t\t{sql connect=\"" . $connect . "\" query=\"" . $v["sql"] . "\"}\n";
				} else {
					$htmlstr .= "\t\t\t\t\t\t\t\t\t\t{sql query=\"" . $v["sql"] . "\"}\n";
				}
				$sqlvalue = [];
				$all = [];
				preg_match_all("/select(.*)from/iUs", $v["sql"], $all);
				if (!empty($all[1][0])) {
					$sqlvalue = explode(",", $all[1][0]);
					foreach ($sqlvalue as $key => $val) {
						if (preg_match("/[\\s]+as[\\s]+/", strtolower($val))) {
							$sqlvalue[$key] = preg_split("/\\s+/", $val)[2];
						}
					}
				}
				$htmlstr .= "\t\t\t\t\t\t\t\t\t\t<option value=\"{\$sql." . trim($sqlvalue[0]) . "}\">{\$sql." . trim($sqlvalue[1]) . "}</option>\n";
				$htmlstr .= "\t\t\t\t\t\t\t\t\t\t{/sql}\n";
			}
			$htmlstr .= "\t\t\t\t\t\t\t\t\t</select>\n";
		}
		$htmlstr .= "\t\t\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
		return $htmlstr;
	}
	public static function formGroup($fieldInfo, $type, $applicationInfo, $menuInfo)
	{
		switch ($fieldInfo["type"]) {
			case 1:
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
			case 2:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
					$str .= "\t\t\t\t\t\t\t<?php if(!isset(\$info['" . $fieldInfo["field"] . "'])){ \$info['" . $fieldInfo["field"] . "'] = '" . $defaultValue . "'; }; ?>\n";
				}
				$str .= "\t\t\t\t\t\t\t<select lay-ignore name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" id=\"" . $fieldInfo["field"] . "\">\n";
				$str .= "\t\t\t\t\t\t\t\t<option value=\"\">请选择</option>\n";
				if (empty($fieldInfo["sql"])) {
					$searchArr = explode(",", $fieldInfo["config"]);
					if ($searchArr) {
						foreach ($searchArr as $k => $v) {
							$varArr = explode("|", $v);
							$str .= "\t\t\t\t\t\t\t\t<option value=\"" . $varArr[1] . "\" {if condition=\"\$info." . $fieldInfo["field"] . " eq '" . $varArr[1] . "'\"}selected{/if}>" . $varArr[0] . "</option>\n";
						}
					}
				} else {
					$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
					if ($connect) {
						$str .= "\t\t\t\t\t\t\t\t{sql connect=\"" . $connect . "\" query=\"" . $fieldInfo["sql"] . "\"}\n";
					} else {
						$str .= "\t\t\t\t\t\t\t\t{sql query=\"" . $fieldInfo["sql"] . "\"}\n";
					}
					$sqlvalue = [];
					$all = [];
					preg_match_all("/select(.*)from/iUs", $fieldInfo["sql"], $all);
					if (!empty($all[1][0])) {
						$sqlvalue = explode(",", $all[1][0]);
						foreach ($sqlvalue as $key => $val) {
							if (preg_match("/[\\s]+as[\\s]+/", strtolower($val))) {
								$sqlvalue[$key] = preg_split("/\\s+/", $val)[2];
							}
						}
					}
					$str .= "\t\t\t\t\t\t\t\t\t<option value=\"{\$sql." . trim($sqlvalue[0]) . "}\" {if condition=\"\$info." . $fieldInfo["field"] . " eq \$sql." . trim($sqlvalue[0]) . "\"}selected{/if}>{\$sql." . trim($sqlvalue[1]) . "}</option>\n";
					$str .= "\t\t\t\t\t\t\t\t{/sql}\n";
				}
				$str .= "\t\t\t\t\t\t\t</select>\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 27:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
					$str .= "\t\t\t\t\t\t\t<?php if(!isset(\$info['" . $fieldInfo["field"] . "'])){ \$info['" . $fieldInfo["field"] . "'] = '" . $defaultValue . "'; }; ?>\n";
				}
				$str .= "\t\t\t\t\t\t\t<select lay-ignore name=\"" . $fieldInfo["field"] . "\" class=\"form-control chosen\" multiple data-placeholder='请选择" . $fieldInfo["name"] . "'  id=\"" . $fieldInfo["field"] . "\">\n";
				if (empty($fieldInfo["sql"])) {
					$searchArr = explode(",", $fieldInfo["config"]);
					if ($searchArr) {
						foreach ($searchArr as $k => $v) {
							$varArr = explode("|", $v);
							$str .= "\t\t\t\t\t\t\t\t<option value=\"" . $varArr[1] . "\" {if in_array(\"" . $varArr[1] . "\",explode(',',\$info." . $fieldInfo["field"] . "))}selected{/if}>" . $varArr[0] . "</option>\n";
						}
					}
				} else {
					$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
					if ($connect) {
						$str .= "\t\t\t\t\t\t\t\t{sql connect=\"" . $connect . "\" query=\"" . $fieldInfo["sql"] . "\"}\n";
					} else {
						$str .= "\t\t\t\t\t\t\t\t{sql query=\"" . $fieldInfo["sql"] . "\"}\n";
					}
					$sqlvalue = [];
					$all = [];
					preg_match_all("/select(.*)from/iUs", $fieldInfo["sql"], $all);
					if (!empty($all[1][0])) {
						$sqlvalue = explode(",", $all[1][0]);
						foreach ($sqlvalue as $key => $val) {
							if (preg_match("/[\\s]+as[\\s]+/", strtolower($val))) {
								$sqlvalue[$key] = preg_split("/\\s+/", $val)[2];
							}
						}
					}
					$str .= "\t\t\t\t\t\t\t\t\t<option value=\"{\$sql." . trim($sqlvalue[0]) . "}\" {if in_array(\$sql." . trim($sqlvalue[0]) . ",explode(',',\$info['" . $fieldInfo["field"] . "']))}selected{/if}>{\$sql." . trim($sqlvalue[1]) . "}</option>\n";
					$str .= "\t\t\t\t\t\t\t\t{/sql}\n";
				}
				$str .= "\t\t\t\t\t\t\t</select>\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 29:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
					$str .= "\t\t\t\t\t\t\t<?php if(!isset(\$info['" . $fieldInfo["field"] . "'])){ \$info['" . $fieldInfo["field"] . "'] = '" . $defaultValue . "'; }; ?>\n";
				}
				$str .= "\t\t\t\t\t\t\t<select lay-ignore name=\"" . $fieldInfo["field"] . "\" class=\"form-control chosen\" data-placeholder='请选择" . $fieldInfo["name"] . "'  id=\"" . $fieldInfo["field"] . "\">\n";
				$str .= "\t\t\t\t\t\t\t\t<option value=\"\">请选择</option>\n";
				if (empty($fieldInfo["sql"])) {
					$searchArr = explode(",", $fieldInfo["config"]);
					if ($searchArr) {
						foreach ($searchArr as $k => $v) {
							$varArr = explode("|", $v);
							$str .= "\t\t\t\t\t\t\t\t<option value=\"" . $varArr[1] . "\" {if condition=\"\$info." . $fieldInfo["field"] . " eq '" . $varArr[1] . "'\"}selected{/if}>" . $varArr[0] . "</option>\n";
						}
					}
				} else {
					$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
					if ($connect) {
						$str .= "\t\t\t\t\t\t\t\t{sql connect=\"" . $connect . "\" query=\"" . $fieldInfo["sql"] . "\"}\n";
					} else {
						$str .= "\t\t\t\t\t\t\t\t{sql query=\"" . $fieldInfo["sql"] . "\"}\n";
					}
					$sqlvalue = [];
					$all = [];
					preg_match_all("/select(.*)from/iUs", $fieldInfo["sql"], $all);
					if (!empty($all[1][0])) {
						$sqlvalue = explode(",", $all[1][0]);
						foreach ($sqlvalue as $key => $val) {
							if (preg_match("/[\\s]+as[\\s]+/", strtolower($val))) {
								$sqlvalue[$key] = preg_split("/\\s+/", $val)[2];
							}
						}
					}
					$str .= "\t\t\t\t\t\t\t\t\t<option value=\"{\$sql." . trim($sqlvalue[0]) . "}\" {if condition=\"\$info." . $fieldInfo["field"] . " eq \$sql." . trim($sqlvalue[0]) . "\"}selected{/if}>{\$sql." . trim($sqlvalue[1]) . "}</option>\n";
					$str .= "\t\t\t\t\t\t\t\t{/sql}\n";
				}
				$str .= "\t\t\t\t\t\t\t</select>\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 3:
				$str .= "\t\t\t\t\t<div class=\"form-group layui-form\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if (empty($fieldInfo["sql"])) {
					$valArr = explode(",", $fieldInfo["config"]);
					$value = \strval($fieldInfo["default_value"]);
					if (empty($value) && $value != "0") {
						$defaultValue = explode("|", $valArr[0])[1];
					} else {
						$defaultValue = $fieldInfo["default_value"];
					}
					$str .= "\t\t\t\t\t\t\t<?php if(!isset(\$info['" . $fieldInfo["field"] . "'])){ \$info['" . $fieldInfo["field"] . "'] = '" . $defaultValue . "'; }; ?>\n";
					if ($valArr) {
						foreach ($valArr as $k => $v) {
							$varArr = explode("|", $v);
							$str .= "\t\t\t\t\t\t\t<input name=\"" . $fieldInfo["field"] . "\" value=\"" . $varArr[1] . "\" type=\"radio\" {if condition=\"\$info." . $fieldInfo["field"] . " eq '" . $varArr[1] . "'\"}checked{/if} title=\"" . $varArr[0] . "\">\n";
						}
					}
				} else {
					if ($type == 3 && !is_null($fieldInfo["default_value"])) {
						$defaultValue = $fieldInfo["default_value"];
						if ($defaultValue) {
							$str .= "\t\t\t\t\t\t\t<?php if(!isset(\$info['" . $fieldInfo["field"] . "'])){ \$info['" . $fieldInfo["field"] . "'] = " . $defaultValue . "; }; ?>\n";
						}
					}
					$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
					if ($connect) {
						$str .= "\t\t\t\t\t\t\t\t{sql connect=\"" . $connect . "\" query=\"" . $fieldInfo["sql"] . "\"}\n";
					} else {
						$str .= "\t\t\t\t\t\t\t\t{sql query=\"" . $fieldInfo["sql"] . "\"}\n";
					}
					$sqlvalue = [];
					$all = [];
					preg_match_all("/select(.*)from/iUs", $fieldInfo["sql"], $all);
					if (!empty($all[1][0])) {
						$sqlvalue = explode(",", $all[1][0]);
						foreach ($sqlvalue as $key => $val) {
							if (preg_match("/[\\s]+as[\\s]+/", strtolower($val))) {
								$sqlvalue[$key] = preg_split("/\\s+/", $val)[2];
							}
						}
					}
					$str .= "\t\t\t\t\t\t\t<input name=\"" . $fieldInfo["field"] . "\" value=\"{\$sql." . trim($sqlvalue[0]) . "}\" type=\"radio\" {if condition=\"\$info." . $fieldInfo["field"] . " eq \$sql." . trim($sqlvalue[0]) . "\"}checked{/if} title=\"{\$sql." . trim($sqlvalue[1]) . "}\">\n";
					$str .= "\t\t\t\t\t\t\t\t{/sql}\n";
				}
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 4:
				$str .= "\t\t\t\t\t<div class=\"form-group layui-form\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
					$str .= "\t\t\t\t\t\t\t<?php if(!isset(\$info['" . $fieldInfo["field"] . "'])){ \$info['" . $fieldInfo["field"] . "'] = '" . $defaultValue . "'; }; ?>\n";
				}
				if (empty($fieldInfo["sql"])) {
					$searchArr = explode(",", $fieldInfo["config"]);
					if ($searchArr) {
						foreach ($searchArr as $k => $v) {
							$varArr = explode("|", $v);
							$str .= "\t\t\t\t\t\t\t\t<input name=\"" . $fieldInfo["field"] . "\" value=\"" . $varArr[1] . "\" type=\"checkbox\" {if in_array(" . $varArr[1] . ",explode(',',\$info['" . $fieldInfo["field"] . "']))}checked{/if} title=\"" . $varArr[0] . "\">\n";
						}
					}
				} else {
					$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
					if ($connect) {
						$str .= "\t\t\t\t\t\t\t\t{sql connect=\"" . $connect . "\" query=\"" . $fieldInfo["sql"] . "\"}\n";
					} else {
						$str .= "\t\t\t\t\t\t\t\t{sql query=\"" . $fieldInfo["sql"] . "\"}\n";
					}
					$sqlvalue = [];
					$all = [];
					preg_match_all("/select(.*)from/iUs", $fieldInfo["sql"], $all);
					if (!empty($all[1][0])) {
						$sqlvalue = explode(",", $all[1][0]);
						foreach ($sqlvalue as $key => $val) {
							if (preg_match("/[\\s]+as[\\s]+/", strtolower($val))) {
								$sqlvalue[$key] = preg_split("/\\s+/", $val)[2];
							}
						}
					}
					$str .= "\t\t\t\t\t\t\t\t\t<input name=\"" . $fieldInfo["field"] . "\" value=\"{\$sql." . trim($sqlvalue[0]) . "}\" type=\"checkbox\" {if in_array(\$sql." . trim($sqlvalue[0]) . ",explode(',',\$info['" . $fieldInfo["field"] . "']))}checked{/if} title=\"{\$sql." . trim($sqlvalue[1]) . "}\">\n";
					$str .= "\t\t\t\t\t\t\t\t{/sql}\n";
				}
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 5:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t<input type=\"password\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 6:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t<textarea id=\"" . $fieldInfo["field"] . "\" name=\"" . $fieldInfo["field"] . "\"  class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">" . $defaultValue . "</textarea>\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 7:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				$default_time_format = explode("|", $fieldInfo["default_value"]);
				if (!$fieldInfo["default_value"] || $fieldInfo["default_value"] == "null") {
					$time_format = "Y-m-d H:i:s";
				} else {
					$time_format = $default_time_format[0];
				}
				if ($default_time_format[1] == "null" || $fieldInfo["default_value"] == "null") {
					$time = "";
				} else {
					$time = "{:date('" . $time_format . "')}";
				}
				if ($type == 4) {
					$time = "{if condition=\"\$info." . $fieldInfo["field"] . " neq 0\"}{\$info." . $fieldInfo["field"] . "|date='" . $time_format . "'}{/if}";
				}
				$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" value=\"" . $time . "\" name=\"" . $fieldInfo["field"] . "\"  placeholder=\"请输入" . $fieldInfo["name"] . "\" class=\"form-control\" id=\"" . $fieldInfo["field"] . "\">\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 8:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-6\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" {if condition=\"config('my.img_show_status') eq true\"}onmousemove=\"showBigPic(this.value)\" onmouseout=\"closeimg()\"{/if} name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
				$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none " . $fieldInfo["field"] . "_process\">" . $fieldInfo["note"] . "</span>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-2\" style=\"position:relative; right:30px;\">\n";
				$str .= "\t\t\t\t\t\t\t<span id=\"" . $fieldInfo["field"] . "_upload\"></span>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 9:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-6\">\n";
				$str .= "\t\t\t\t\t\t\t<div class=\"" . $fieldInfo["field"] . " pic_list\">\n";
				$str .= "\t\t\t\t\t\t\t\t<li id=\"" . $fieldInfo["field"] . "_upload\"></li>\n";
				$str .= "\t\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t\t<div style=\"clear:both\"></div>\n";
				$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none " . $fieldInfo["field"] . "_process\">" . $fieldInfo["note"] . "</span>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 10:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-6\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
				$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none " . $fieldInfo["field"] . "_process\">" . $fieldInfo["note"] . "</span>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-3\" style=\"position:relative; right:30px;\">\n";
				$str .= "\t\t\t\t\t\t\t<span id=\"" . $fieldInfo["field"] . "_upload\"></span>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 34:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-8\">\n";
				$str .= "\t\t\t\t\t\t\t<span id=\"" . $fieldInfo["field"] . "_upload\"></span>\n";
				$str .= "\t\t\t\t\t\t\t<div class=\"" . $fieldInfo["field"] . "\"></div>\n";
				$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none " . $fieldInfo["field"] . "_process\">" . $fieldInfo["note"] . "</span>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 11:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t\t<textarea id=\"" . $fieldInfo["field"] . "\" name=\"" . $fieldInfo["field"] . "\" style=\"width: 100%; height:300px;\">" . $defaultValue . "</textarea>\n";
				if ($menuInfo["upload_config_id"]) {
					$str .= "\t\t\t\t\t\t\t\t<script type=\"text/javascript\">\$('#" . $fieldInfo["field"] . "').xheditor({html5Upload:false,upLinkUrl:\"{:url('" . $applicationInfo["app_dir"] . "/Upload/editorUpload',['upload_config_id'=>" . $menuInfo["upload_config_id"] . "])}\",upLinkExt:\"zip,rar,txt,doc,docx,pdf,xls,xlsx\",upImgUrl:\"{:url('" . $applicationInfo["app_dir"] . "/Upload/editorUpload',['upload_config_id'=>" . $menuInfo["upload_config_id"] . "])}\",upImgExt:\"jpg,jpeg,gif,png\"});</script>\n";
				} else {
					$str .= "\t\t\t\t\t\t\t\t<script type=\"text/javascript\">\$('#" . $fieldInfo["field"] . "').xheditor({html5Upload:false,upLinkUrl:\"{:url('" . $applicationInfo["app_dir"] . "/Upload/editorUpload')}\",upLinkExt:\"zip,rar,txt,doc,docx,pdf,xls,xlsx\",upImgUrl:\"{:url('" . $applicationInfo["app_dir"] . "/Upload/editorUpload')}\",upImgExt:\"jpg,jpeg,gif,png\"});</script>\n";
				}
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 12:
				if ($type == 4) {
					$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
					$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
					$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
					$default_time_format = explode("|", $fieldInfo["default_value"]);
					$time_format = $default_time_format[0];
					if (!$time_format || $fieldInfo["default_value"] == "null") {
						$time_format = "Y-m-d H:i:s";
					}
					$time = "{if condition=\"\$info." . $fieldInfo["field"] . " neq ''\"}{\$info." . $fieldInfo["field"] . "|date='" . $time_format . "'}{/if}";
					$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" value=\"" . $time . "\" name=\"" . $fieldInfo["field"] . "\"  placeholder=\"请输入" . $fieldInfo["name"] . "\" class=\"form-control\" id=\"" . $fieldInfo["field"] . "\">\n";
					if (!empty($fieldInfo["note"])) {
						$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
					}
					$str .= "\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t</div>\n";
				}
				break;
			case 13:
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
			case 14:
				if ($type == 3) {
					if ($fieldInfo["default_value"] || $fieldInfo["default_value"] == "0") {
						$defaultValue = $fieldInfo["default_value"];
					} else {
						$defaultValue = "{\$Request.get." . $fieldInfo["field"] . "}";
					}
					$str .= "\t\t\t\t\t<input type=\"hidden\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\">\n";
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
					$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
					$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
					$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
					$str .= "\t\t\t\t\t\t\t<input type=\"text\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
					if (!empty($fieldInfo["note"])) {
						$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
					}
					$str .= "\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t</div>\n";
				}
				break;
			case 16:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t<script id=\"" . $fieldInfo["field"] . "\" type=\"text/plain\" name=\"" . $fieldInfo["field"] . "\" style=\"width:100%;height:300px;\">" . $defaultValue . "</script>\n";
				$str .= "\t\t\t\t\t\t\t<script type=\"text/javascript\">\n";
				if ($menuInfo["upload_config_id"]) {
					$str .= "\t\t\t\t\t\t\t\tvar ue = UE.getEditor('" . $fieldInfo["field"] . "',{serverUrl : '{:url(\"" . $applicationInfo["app_dir"] . "/Upload/uploadUeditor\",[\"upload_config_id\"=>" . $menuInfo["upload_config_id"] . "])}'});\n";
				} else {
					$str .= "\t\t\t\t\t\t\t\tvar ue = UE.getEditor('" . $fieldInfo["field"] . "',{serverUrl : '{:url(\"" . $applicationInfo["app_dir"] . "/Upload/uploadUeditor\")}'});\n";
				}
				$str .= "\t\t\t\t\t\t\t\tscaleEnabled:true\n";
				$str .= "\t\t\t\t\t\t\t</script>\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 17:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"distpicker5\">\n";
				foreach (explode("|", $fieldInfo["field"]) as $k => $v) {
					if ($k == "0") {
						$areaTitle = "province";
					} elseif ($k == "1") {
						$areaTitle = "city";
					} elseif ($k == "2") {
						$areaTitle = "district";
					}
					$str .= "\t\t\t\t\t\t\t<div class=\"col-sm-3\">\n";
					if ($type == 3 && !empty($fieldInfo["default_value"])) {
						$defaultValue = explode("|", $fieldInfo["default_value"]);
						if (!empty($defaultValue[$k])) {
							$str .= "\t\t\t\t\t\t\t<?php if(!isset(\$info['" . $v . "'])){ \$info['" . $v . "'] = '" . $defaultValue[$k] . "'; }; ?>\n";
						}
					}
					$str .= "\t\t\t\t\t\t\t\t<select lay-ignore id=\"" . $v . "\" class=\"form-control\" data-" . $areaTitle . "=\"{\$info." . $v . "}\"></select>\n";
					$str .= "\t\t\t\t\t\t\t</div>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t<script src=\"__PUBLIC__/static/js/plugins/shengshiqu/distpicker.data.js\"></script>\n";
				$str .= "\t\t\t\t\t<script src=\"__PUBLIC__/static/js/plugins/shengshiqu/distpicker.js\"></script>\n";
				$str .= "\t\t\t\t\t<script src=\"__PUBLIC__/static/js/plugins/shengshiqu/main.js\"></script>\n";
				break;
			case 18:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"" . $fieldInfo["field"] . "\">\n";
				$str .= "\t\t\t\t\t\t\t<div class=\"col-sm-8\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t\t<input type=\"text\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t\t<div class=\"col-sm-1\">\n";
				$str .= "\t\t\t\t\t\t\t\t<span style=\"border:none; margin-left:-30px;  padding:0;\" class=\"input-group-addon col-sm-2\"><i style=\"width:32px; height:32px;\"></i></span>\n";
				$str .= "\t\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t<link href=\"__PUBLIC__/static/js/plugins/colorpicker/bootstrap-colorpicker.css\" rel=\"stylesheet\">\n";
				$str .= "\t\t\t\t\t<script src=\"__PUBLIC__/static/js/plugins/colorpicker/bootstrap-colorpicker.js\"></script>\n";
				$str .= "\t\t\t\t\t<script type=\"text/javascript\">\n";
				$str .= "\t\t\t\t\t\$(function () {\n";
				$str .= "\t\t\t\t\t\t\$('." . $fieldInfo["field"] . "').colorpicker();\n";
				$str .= "\t\t\t\t\t\t{if condition='\$info." . $fieldInfo["field"] . " eq \"\"'}\n";
				$str .= "\t\t\t\t\t\t\t\$('#" . $fieldInfo["field"] . "').val('');\n";
				$str .= "\t\t\t\t\t\t{/if}\n";
				$str .= "\t\t\t\t\t});\n";
				$str .= "\t\t\t\t\t</script>\n";
				break;
			case 19:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				$str .= "\t\t\t\t\t\t\t<div class=\"input-group\" id=\"" . $fieldInfo["field"] . "_address\">\n";
				$str .= "\t\t\t\t\t\t\t<textarea id=\"" . $fieldInfo["field"] . "\" name=\"" . $fieldInfo["field"] . "\"  class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">{\$info." . $fieldInfo["field"] . "}</textarea>\n";
				$str .= "\t\t\t\t\t\t\t\t<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-map-marker\"></span></span>\n";
				$str .= "\t\t\t\t\t\t\t</div>\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t<script type=\"text/javascript\" src=\"https://webapi.amap.com/maps?v=1.3&key=ed1fafa0307bb4991da41f54d8a88b46\"></script>\n";
				$str .= "\t\t\t\t\t<script src=\"__PUBLIC__/static/js/plugins/map/bootstrap.AMapPositionPicker.js\"></script>\n";
				$str .= "\t\t\t\t\t<script type=\"text/javascript\">\n";
				$str .= "\t\t\t\t\t\$(function () {\n";
				$str .= "\t\t\t\t\t\tvar p = \$(\"#" . $fieldInfo["field"] . "_address\").AMapPositionPicker();\n";
				$str .= "\t\t\t\t\t});\n";
				$str .= "\t\t\t\t\t</script>\n";
				break;
			case 35:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				$str .= "\t\t\t\t\t\t\t<div class=\"input-group\" data-toggle=\"modal\" data-target=\"#" . $fieldInfo["field"] . "Modal\">\n";
				$str .= "\t\t\t\t\t\t\t<textarea id=\"" . $fieldInfo["field"] . "\" readonly name=\"" . $fieldInfo["field"] . "\"  class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">{\$info." . $fieldInfo["field"] . "}</textarea>\n";
				$str .= "\t\t\t\t\t\t\t\t<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-map-marker\"></span></span>\n";
				$str .= "\t\t\t\t\t\t\t</div>\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t<div class=\"modal fade\" id=\"" . $fieldInfo["field"] . "Modal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"" . $fieldInfo["field"] . "ModalLabel\">\n";
				$str .= "\t\t\t\t\t\t<div class=\"modal-dialog\" role=\"document\">\n";
				$str .= "\t\t\t\t\t\t\t<div class=\"modal-content\">\n";
				$str .= "\t\t\t\t\t\t\t\t<div class=\"modal-header\">\n";
				$str .= "\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">×</span></button>\n";
				$str .= "\t\t\t\t\t\t\t\t\t\t<h4 class=\"modal-title\" id=\"" . $fieldInfo["field"] . "ModalLabel\">请选择地址</h4>\n";
				$str .= "\t\t\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t\t\t<div class=\"modal-body\">\n";
				$str .= "\t\t\t\t\t\t\t\t\t<div class=\"case\" style=\"height:350px;\">\n";
				$str .= "\t\t\t\t\t\t\t\t\t\t<div class=\"bMap\" id='" . $fieldInfo["field"] . "Map'></div>\n";
				$str .= "\t\t\t\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t\t\t<div class=\"modal-footer\">\n";
				$str .= "\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-primary queren" . $fieldInfo["field"] . "\">确认</button>\n";
				$str .= "\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">取消</button>\n";
				$str .= "\t\t\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t<script type=\"text/javascript\" src=\"http://api.map.baidu.com/api?v=2.0&ak=EZPCgQ6zGu6hZSmXlRrUMTpr\"></script>\n";
				$str .= "\t\t\t\t\t<script type=\"text/javascript\" src=\"__PUBLIC__/static/js/plugins/map/map.jquery.min.js\"></script>\n";
				$str .= "\t\t\t\t\t<script type=\"text/javascript\">\n";
				$str .= "\t\t\t\t\t\$('#" . $fieldInfo["field"] . "Modal').on('show.bs.modal', function (event) {\n";
				$str .= "\t\t\t\t\t\tvar button = \$(event.relatedTarget) // 触发事件的按钮\n";
				$str .= "\t\t\t\t\t});\n";
				$str .= "\t\t\t\t\t\$(\"#" . $fieldInfo["field"] . "Map\").bMap({name:\"callback\",callback:function(address,point){\n";
				$str .= "\t\t\t\t\t\t\$(\".queren" . $fieldInfo["field"] . "\").on('click',function(){\n";
				$str .= "\t\t\t\t\t\t\tvar addre = {'longitude':point.lng,'latitude':point.lat,'address':\$(\"#Map_input_callback\").val()};\n";
				$str .= "\t\t\t\t\t\t\t\$(\"#" . $fieldInfo["field"] . "\").val(JSON.stringify(addre));\n";
				$str .= "\t\t\t\t\t\t\t\$(\"#" . $fieldInfo["field"] . "Modal\").modal('hide');\n";
				$str .= "\t\t\t\t\t\t});\n";
				$str .= "\t\t\t\t\t}});\n";
				$str .= "\t\t\t\t\t</script>\n";
				break;
			case 20:
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
			case 21:
				if ($type == 4) {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
					$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
					$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
					$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
					$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
					if (!empty($fieldInfo["note"])) {
						$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
					}
					$str .= "\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t</div>\n";
				}
				break;
			case 22:
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
			case 28:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = $fieldInfo["default_value"];
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" class=\"form-control\" data-role=\"tagsinput\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\"  >\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 23:
				$str .= "\t\t\t\t\t<div class=\"form-group layui-form\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				$valArr = explode(",", $fieldInfo["config"]);
				if ($valArr) {
					$value = \strval($fieldInfo["default_value"]);
					if (empty($value) && $value != "0") {
						$defaultValue = explode("|", $valArr[0])[1];
					} else {
						$defaultValue = $fieldInfo["default_value"];
					}
					$str .= "\t\t\t\t\t\t\t<?php if(!isset(\$info['" . $fieldInfo["field"] . "'])){ \$info['" . $fieldInfo["field"] . "'] = " . $defaultValue . "; }; ?>\n";
					if ($valArr) {
						foreach ($valArr as $k => $v) {
							$varArr = explode("|", $v);
							$str .= "\t\t\t\t\t\t\t<input name=\"" . $fieldInfo["field"] . "\" value=\"" . $varArr[1] . "\" type=\"radio\" {if condition=\"\$info." . $fieldInfo["field"] . " eq '" . $varArr[1] . "'\"}checked{/if} title=\"" . $varArr[0] . "\">\n";
						}
					}
				}
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 30:
				if ($type == 4) {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
					$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
					$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
					$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
					$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
					if (!empty($fieldInfo["note"])) {
						$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
					}
					$str .= "\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t</div>\n";
				}
				break;
			case 31:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				$str .= "\t\t\t\t\t\t\t<input type=\"text\" autocomplete=\"off\" id=\"" . $fieldInfo["field"] . "\" value=\"" . $defaultValue . "\" name=\"" . $fieldInfo["field"] . "\" class=\"form-control\" placeholder=\"请输入" . $fieldInfo["name"] . "\">\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			case 32:
				if ($type == 3 || $type == 14) {
					$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
					$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
					$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
					$str .= "\t\t\t\t\t\t\t<div class=\"" . $fieldInfo["field"] . "\">\n";
					$str .= "\t\t\t\t\t\t\t\t<div class=\"" . $fieldInfo["field"] . "-line\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t<label class=\"form-inline\" style=\"font-weight:normal;\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" placeholder=\"名称\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t</label>\n";
					$str .= "\t\t\t\t\t\t\t\t\t<label class=\"form-inline\" style=\"font-weight:normal;\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" placeholder=\"值\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t</label>\n";
					$str .= "\t\t\t\t\t\t\t\t\t<label class=\"form-inline btn-group-sm\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-danger cancel\"><i class=\"fa fa-remove\"></i></button>\n";
					$str .= "\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-info move\"><i class=\"fa fa-arrows\"></i></button>\n";
					$str .= "\t\t\t\t\t\t\t\t\t</label>\n";
					$str .= "\t\t\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t\t\t<a  class=\"btn btn-primary btn-xs\" onclick=\"appendToVal('" . $fieldInfo["field"] . "')\"><i class=\"fa fa-plus\"></i>&nbsp;追加</a>\n";
					if (!empty($fieldInfo["note"])) {
						$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
					}
					$str .= "\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t</div>\n";
				}
				if ($type == 4) {
					$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
					$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
					$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
					$str .= "\t\t\t\t\t\t\t<div class=\"" . $fieldInfo["field"] . "\">\n";
					$str .= "\t\t\t\t\t\t\t\t<?php \$" . $fieldInfo["field"] . " = json_decode(\$info['" . $fieldInfo["field"] . "'],true);?>\n";
					$str .= "\t\t\t\t\t\t\t\t{foreach name=\"" . $fieldInfo["field"] . "\" id=\"vo\"}\n";
					$str .= "\t\t\t\t\t\t\t\t<div class=\"" . $fieldInfo["field"] . "-line\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t<label class=\"form-inline\" style=\"font-weight:normal;\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t\t<input type=\"text\" value=\"{\$key}\" class=\"form-control\" placeholder=\"名称\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t</label>\n";
					$str .= "\t\t\t\t\t\t\t\t\t<label class=\"form-inline\" style=\"font-weight:normal;\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t\t<input type=\"text\" value=\"{\$vo}\" class=\"form-control\" placeholder=\"值\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t</label>\n";
					$str .= "\t\t\t\t\t\t\t\t\t<label class=\"form-inline btn-group-sm\">\n";
					$str .= "\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-danger cancel\"><i class=\"fa fa-remove\"></i></button>\n";
					$str .= "\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-info move\"><i class=\"fa fa-arrows\"></i></button>\n";
					$str .= "\t\t\t\t\t\t\t\t\t</label>\n";
					$str .= "\t\t\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t\t\t\t{/foreach}\n";
					$str .= "\t\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t\t\t<a class=\"btn btn-primary btn-xs\" onclick=\"appendToVal('" . $fieldInfo["field"] . "')\"><i class=\"fa fa-plus\"></i>&nbsp;追加</a>\n";
					if (!empty($fieldInfo["note"])) {
						$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
					}
					$str .= "\t\t\t\t\t\t</div>\n";
					$str .= "\t\t\t\t\t</div>\n";
				}
				break;
			case 33:
				$str .= "\t\t\t\t\t<div class=\"form-group\">\n";
				$str .= "\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">" . $fieldInfo["name"] . "：</label>\n";
				$str .= "\t\t\t\t\t\t<div class=\"col-sm-9\">\n";
				if ($type == 3 && !is_null($fieldInfo["default_value"])) {
					$defaultValue = "";
				} else {
					$defaultValue = "{\$info." . $fieldInfo["field"] . "}";
				}
				$str .= "\t\t\t\t\t\t<div id=\"" . $fieldInfo["field"] . "\">\n";
				$str .= "\t\t\t\t\t\t\t<textarea style=\"display:none;\">" . $defaultValue . "</textarea>\n";
				$str .= "\t\t\t\t\t\t\t<link rel=\"stylesheet\" href=\"__PUBLIC__/static/js/meditor/css/editormd.css\" />\n";
				$str .= "\t\t\t\t\t\t\t<script src=\"__PUBLIC__/static/js/meditor/editormd.min.js\"></script>\n";
				$str .= "\t\t\t\t\t\t\t<script type=\"text/javascript\">\n";
				$str .= "\t\t\t\t\t\t\t\tvar " . $fieldInfo["field"] . ";\n";
				$str .= "\t\t\t\t\t\t\t\t\$(function() {\n";
				$str .= "\t\t\t\t\t\t\t\t\t" . $fieldInfo["field"] . " = editormd(\"" . $fieldInfo["field"] . "\", {\n";
				$str .= "\t\t\t\t\t\t\t\t\t\twidth   : \"100%\",\n";
				$str .= "\t\t\t\t\t\t\t\t\t\theight  : 600,\n";
				$str .= "\t\t\t\t\t\t\t\t\t\tsyncScrolling : \"single\",\n";
				$str .= "\t\t\t\t\t\t\t\t\t\tpath    : \"__PUBLIC__/static/js/meditor/lib/\",\n";
				$str .= "\t\t\t\t\t\t\t\t\t\timageUpload : true,\n";
				$str .= "\t\t\t\t\t\t\t\t\t\timageFormats : [\"jpg\",\"jpeg\",\"gif\",\"png\",\"bmp\",\"webp\"],\n";
				$str .= "\t\t\t\t\t\t\t\t\t\timageUploadURL : \"{:url('" . $applicationInfo["app_dir"] . "/Upload/markDownUpload')}\",\n";
				$str .= "\t\t\t\t\t\t\t\t\t\tsaveHTMLToTextarea : true\n";
				$str .= "\t\t\t\t\t\t\t\t\t});\n";
				$str .= "\t\t\t\t\t\t\t\t});\n";
				$str .= "\t\t\t\t\t\t\t</script>\n";
				$str .= "\t\t\t\t\t\t</div>\n";
				if (!empty($fieldInfo["note"])) {
					$str .= "\t\t\t\t\t\t\t<span class=\"help-block m-b-none\">" . $fieldInfo["note"] . "</span>\n";
				}
				$str .= "\t\t\t\t\t\t</div>\n";
				$str .= "\t\t\t\t\t</div>\n";
				break;
			default:
				$str .= ExtendService::getExtendFieldList($fieldInfo, $type, $applicationInfo, $menuInfo);
		}
		return $str;
	}
	public static function getRelateFieldList($table_name)
	{
		$where["b.app_type"] = 1;
		$where["a.table_name"] = $table_name;
		$menuInfo = db("menu")->field("a.*,b.*")->alias("a")->join("application b", "a.app_id=b.app_id", "LEFT")->where($where)->find();
		try {
			$map["is_post"] = 1;
			$map["menu_id"] = $menuInfo["menu_id"];
			$fieldList = \app\gcadmin\controller\Sys\model\Field::where($map)->select()->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $fieldList;
	}
	public static function getFieldType($fieldName, $menu_id)
	{
		$info = \app\gcadmin\controller\Sys\model\Field::where(["field" => $fieldName, "menu_id" => $menu_id])->find();
		if ($info) {
			return $info->type;
		}
	}
	public static function getFieldStatus($field, $table_name, $connect)
	{
		$list = \think\facade\Db::connect($connect)->query("show full columns from " . config("database.connections." . $connect . ".prefix") . $table_name);
		foreach ($list as $key => $val) {
			$fields[] = $val["Field"];
		}
		foreach (explode("|", $field) as $k => $v) {
			if (in_array($v, $fields)) {
				return true;
			}
		}
	}
	public static function checkValidateStatus($fields, $validata)
	{
		$str = false;
		foreach (explode(",", $fields) as $key => $val) {
			if (in_array($val, $validata)) {
				$str = true;
			}
		}
		return $str;
	}
	public static function array_unset_tt($arr, $key)
	{
		$res = [];
		foreach ($arr as $value) {
			if (isset($res[$value[$key]])) {
				unset($value[$key]);
			} else {
				$res[$value[$key]] = $value;
			}
		}
		return $res;
	}
}