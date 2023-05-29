<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\service;

class FormExtendService
{
	public function saveData($formData)
	{
		$fieldList = db("field")->where("menu_id", $formData["form_id"])->select();
		if (!$fieldList) {
			throw new \think\exception\ValidateException("模型不存在");
		}
		$extInfo = db("menu")->where("menu_id", $formData["form_id"])->find();
		if (!$extInfo["is_submit"]) {
			throw new \think\exception\ValidateException("禁止投稿");
		}
		foreach ($fieldList as $key => $v) {
			$rule = [];
			if (!empty($v["validate"]) || !empty($v["rule"])) {
				if (in_array("notEmpty", explode(",", $v["validate"]))) {
					array_push($rule, "require");
					$msg[$v["field"] . ".require"] = $v["name"] . "不能为空";
				}
				if (in_array("unique", explode(",", $v["validate"]))) {
					array_push($rule, "unique:" . $extInfo["table_name"]);
					$msg[$v["field"] . ".unique"] = $v["name"] . "已存在";
				}
				if (!empty($v["rule"])) {
					$rule["regex"] = html_out($v["rule"]);
					$msg[$v["field"] . ".regex"] = $v["name"] . "格式错误";
				}
				$rules[$v["field"]] = $rule;
			}
			if ($v["type"] == 7) {
				$formData[$v["field"]] = strtotime($formData[$v["field"]]);
			}
			if ($v["type"] == 12) {
				$formData[$v["field"]] = time();
			}
			if ($v["type"] == 20) {
				$formData[$v["field"]] = request()->ip();
			}
		}
		$validate = \think\facade\Validate::rule($rules)->message($msg);
		if (!$validate->check($formData)) {
			throw new \think\exception\ValidateException($validate->getError());
		}
		try {
			db($extInfo["table_name"])->insertGetId($formData);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return true;
	}
}