<?php

//decode by http://www.yunlu99.com/
error_reporting(0);
if (!function_exists("db")) {
	function db($name = "", $connect = "")
	{
		if (empty($connect)) {
			$connect = config("database.default");
		}
		return \think\facade\Db::connect($connect, false)->name($name);
	}
}
function random($length = 10, $type = "letter", $convert = 0)
{
	$config = ["number" => "1234567890", "letter" => "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", "string" => "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789", "all" => "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"];
	if (!isset($config[$type])) {
		$type = "letter";
	}
	$string = $config[$type];
	$code = "";
	$strlen = strlen($string) - 1;
	for ($i = 0; $i < $length; $i++) {
		$code .= $string[mt_rand(0, $strlen)];
	}
	if (!empty($convert)) {
		$code = $convert > 0 ? strtoupper($code) : strtolower($code);
	}
	return $code;
}
function doOrderSn($type)
{
	return date("YmdHis") . $type . substr(microtime(), 2, 3) . sprintf("%02d", rand(0, 99));
}
function deldir($dir)
{
	$dh = opendir($dir);
	while ($file = readdir($dh)) {
		if ($file != "." && $file != "..") {
			$fullpath = $dir . "/" . $file;
			if (!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				deldir($fullpath);
			}
		}
	}
	closedir($dh);
	if (rmdir($dir)) {
		return true;
	} else {
		return false;
	}
}
function data_auth_sign($data)
{
	if (!is_array($data)) {
		$data = (array) $data;
	}
	ksort($data);
	$code = http_build_query($data);
	$sign = sha1($code);
	return $sign;
}
function getFieldVal($val, $fieldConfig)
{
	if ($fieldConfig) {
		foreach (explode(",", $fieldConfig) as $k => $v) {
			$tempstr = explode("|", $v);
			foreach (explode(",", $val) as $m => $n) {
				if ($tempstr[1] == $n) {
					$fieldvals .= $tempstr[0] . ",";
				}
			}
		}
		return rtrim($fieldvals, ",");
	}
}
function getFieldName($val, $fieldConfig)
{
	if ($fieldConfig) {
		foreach (explode(",", $fieldConfig) as $k => $v) {
			$tempstr = explode("|", $v);
			if ($tempstr[0] == $val) {
				$fieldval = $tempstr[1];
			}
		}
		return $fieldval;
	}
}
function getKeyByVal($array, $data)
{
	foreach ($array as $key => $val) {
		if ($val == $data) {
			$data = $key;
		}
	}
	return $data;
}
function formartExportWhere($field)
{
	foreach ($field as $k => $v) {
		if (strpos($v, "|") > 0) {
			$dt = $field[$k];
			unset($field[$k]);
		}
	}
	return \xhadmin\CommonService::filterEmptyArray(array_merge($field, explode("|", $dt)));
}
function formartList($fieldConfig, $list)
{
	$cat = new \org\Category($fieldConfig);
	$ret = $cat->getTree($list);
	return $ret;
}
function filePutContents($content, $filepath, $type)
{
	if (in_array($type, [1, 3])) {
		$str = file_get_contents($filepath);
		$parten = "/\\s\\/\\*+start\\*+\\/(.*)\\/\\*+end\\*+\\//iUs";
		preg_match_all($parten, $str, $all);
		if ($all[0]) {
			foreach ($all[0] as $key => $val) {
				$ext_content .= $val . "\n\n";
			}
		}
		$content .= $ext_content . "\n\n";
		if ($type == 1) {
			$content .= "}\n\n";
		}
	}
	ob_start();
	echo $content;
	$_cache = ob_get_contents();
	ob_end_clean();
	if ($_cache) {
		$File = new \think\template\driver\File();
		$File->write($filepath, $_cache);
	}
}
function htmlOutList($list, $err_status = false)
{
	foreach ($list as $key => $row) {
		$res[$key] = checkData($row, $err_status);
	}
	return $res;
}
function checkData($data, $err_status = true)
{
	if (empty($data) && $err_status) {
		abort(412, "没有数据");
	}
	if (is_object($data)) {
		$data = $data->toArray();
	}
	foreach ($data as $k => $v) {
		if ($v && is_array($v)) {
			$data[$k] = checkData($v);
		} else {
			$data[$k] = html_out($v);
		}
	}
	return $data;
}
function html_in($str)
{
	$str = htmlspecialchars($str);
	$str = strip_tags($str);
	$str = addslashes($str);
	return $str;
}
function html_out($str)
{
	$str = htmlspecialchars_decode($str);
	$str = stripslashes($str);
	return $str;
}
function sql_replace($str)
{
	$farr = ["/insert[\\s]+|update[\\s]+|create[\\s]+|alter[\\s]+|delete[\\s]+|drop[\\s]+|load_file|outfile|dump/is"];
	$str = preg_replace($farr, "", $str);
	return $str;
}
function upload_replace($str)
{
	$farr = ["/php|php3|php4|php5|phtml|pht|/is"];
	$str = preg_replace($farr, "", $str);
	return $str;
}
function serach_in($str)
{
	$farr = ["/^select[\\s]+|insert[\\s]+|and[\\s]+|or[\\s]+|create[\\s]+|update[\\s]+|delete[\\s]+|alter[\\s]+|count[\\s]+|\\'|\\/\\*|\\*|\\.\\.\\/|\\.\\/|union|into|load_file|outfile/i"];
	$str = preg_replace($farr, "", html_in($str));
	return trim($str);
}
function getTimeFormat($val)
{
	$default_time_format = explode("|", $val["default_value"]);
	$time_format = $default_time_format[0];
	if (!$time_format || $val["default_value"] == "null") {
		$time_format = "Y-m-d H:i:s";
	}
	return $time_format;
}
function filterEmptyArray($data = [])
{
	foreach ($data as $k => $v) {
		if (!$v && $v !== 0) {
			unset($data[$k]);
		}
	}
	return $data;
}
function formatWhere($data)
{
	$where = [];
	foreach ($data as $k => $v) {
		if (is_array($v)) {
			if (strval($v[1]) != null && !is_array($v[1]) || is_array($v[1]) && strval($v[1][0]) != null) {
				switch (strtolower($v[0])) {
					case "like":
						$v[1] = "%" . $v[1] . "%";
						break;
					case "exp":
						$v[1] = \think\facade\Db::raw($v[1]);
						break;
				}
				$where[] = [$k, $v[0], $v[1]];
			}
		} else {
			if (strval($v) != null) {
				$where[] = [$k, "=", $v];
			}
		}
	}
	return $where;
}
function getUploadServerUrl($upload_config_id = "")
{
	if (config("my.oss_status") && config("my.oss_upload_type") == "client") {
		$appname = app("http")->getName();
		switch (config("my.oss_default_type")) {
			case "qiniuyun":
				$serverurl = "http://up-z0.qiniup.com?&" . url($appname . "/Base/getOssToken") . "&" . config("my.qny_oss_domain");
				break;
			case "ali":
				$serverurl = getendpoint(config("my.ali_oss_endpoint")) . "?&" . url($appname . "/Base/getOssToken");
				break;
		}
	} else {
		$serverurl = url("Upload/uploadImages", ["upload_config_id" => $upload_config_id]);
	}
	return $serverurl;
}
function getendpoint($str)
{
	if (strpos(config("my.ali_oss_endpoint"), "aliyuncs.com") !== false) {
		if (strpos($str, "https") !== false) {
			$point = "https://" . config("my.ali_oss_bucket") . "." . substr($str, 8);
		} else {
			$point = "http://" . config("my.ali_oss_bucket") . "." . substr($str, 7);
		}
	} else {
		$point = config("my.ali_oss_endpoint");
	}
	return $point;
}
function getTag($key3, $no = 100)
{
	$data = [];
	$key = 65;
	$key2 = 64;
	for ($n = 1; $n <= $no; $n++) {
		if ($key > 90) {
			$key2 += 1;
			$key = 65;
			$data[$n] = chr($key2) . chr($key);
		} else {
			if ($key2 >= 65) {
				$data[$n] = chr($key2) . chr($key);
			} else {
				$data[$n] = chr($key);
			}
		}
		$key += 1;
	}
	return $data[$key3];
}