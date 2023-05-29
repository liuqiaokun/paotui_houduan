<?php

//decode by http://www.yunlu99.com/
error_reporting(0);
if (!function_exists("valid")) {
	function valid($data, $rule)
	{
		$check = true;
		if (is_string($rule)) {
			$rule = explode(",", $rule);
		}
		foreach ($data as $k => $v) {
			if (in_array($k, $rule) && empty($v)) {
				$check = false;
				break;
			}
		}
		foreach ($rule as $k => $v) {
			if (!array_key_exists($v, $data)) {
				$check = false;
				break;
			}
		}
		return $check;
	}
}