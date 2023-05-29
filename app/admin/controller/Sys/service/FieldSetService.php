<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Sys\service;

class FieldSetService
{
	public static function typeField()
	{
		$list = [1 => ["name" => "文本框", "property" => 1], 2 => ["name" => "下拉框(普通)", "property" => 3], 29 => ["name" => "下拉框(带搜索)", "property" => 3], 27 => ["name" => "下拉框(多选)", "property" => 1], 3 => ["name" => "单选框", "property" => 3], 4 => ["name" => "多选框", "property" => 1], 23 => ["name" => "开关按钮", "property" => 6], 5 => ["name" => "密码框", "property" => 1], 6 => ["name" => "文本域", "property" => 4], 7 => ["name" => "日期框", "property" => 2], 31 => ["name" => "时间区间", "property" => 1], 8 => ["name" => "单图上传", "property" => 1], 9 => ["name" => "多图上传", "property" => 4], 10 => ["name" => "单文件上传", "property" => 4], 34 => ["name" => "多文件上传", "property" => 4], 11 => ["name" => "编辑器(xheditor)", "property" => 4], 16 => ["name" => "编辑器(ueditor)", "property" => 4], 33 => ["name" => "markdown编辑器(meditor.md)", "property" => 4], 12 => ["name" => "创建时间(后端录入)", "property" => 2], 25 => ["name" => "修改时间(后端录入)", "property" => 2], 13 => ["name" => "货币", "property" => 5], 20 => ["name" => "整数", "property" => 2], 21 => ["name" => "随机数", "property" => 1], 22 => ["name" => "排序", "property" => 2], 28 => ["name" => "标签", "property" => 1], 14 => ["name" => "隐藏域", "property" => 1], 15 => ["name" => "session值", "property" => 1], 17 => ["name" => "省市区三级联动", "property" => 1], 18 => ["name" => "颜色选择器", "property" => 1], 19 => ["name" => "高德地图坐标选择器", "property" => 1], 35 => ["name" => "百度地图坐标选择器", "property" => 4], 24 => ["name" => "token解码值(用户ID)", "property" => 1], 26 => ["name" => "IP", "property" => 1], 30 => ["name" => "订单号", "property" => 1], 32 => ["name" => "键值对", "property" => 4]];
		return $list;
	}
	public static function propertyField()
	{
		$list = [1 => ["name" => "varchar", "maxlen" => 250, "decimal" => 0], 2 => ["name" => "int", "maxlen" => 11, "decimal" => 0], 3 => ["name" => "smallint", "maxlen" => 6, "decimal" => 0], 4 => ["name" => "text", "maxlen" => 0, "decimal" => 0], 5 => ["name" => "decimal", "maxlen" => 10, "decimal" => 2], 6 => ["name" => "tinyint", "maxlen" => 4, "decimal" => 0]];
		return $list;
	}
	public static function ruleList()
	{
		$list = ["邮箱" => "/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/", "网址" => "/^((ht|f)tps?):\\/\\/([\\w\\-]+(\\.[\\w\\-]+)*\\/)*[\\w\\-]+(\\.[\\w\\-]+)*\\/?(\\?([\\w\\-\\.,@?^=%&:\\/~\\+#]*)+)?/", "货币" => "/(^[1-9]([0-9]+)?(\\.[0-9]{1,2})?\$)|(^(0){1}\$)|(^[0-9]\\.[0-9]([0-9])?\$)/", "数字" => "/^[0-9]*\$/", "手机号" => "/^1[3456789]\\d{9}\$/", "身份证" => "/^[1-9]\\d{5}(18|19|20|(3\\d))\\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\\d{3}[0-9Xx]\$/"];
		return $list;
	}
	public static function dateList()
	{
		$list = ["Y-m-d H:i:s" => "datetime", "Y-m-d" => "date", "Y-m" => "month", "H:i:s" => "time"];
		return $list;
	}
	public static function tabList($menu_id)
	{
		$info = \app\admin\controller\Sys\model\Menu::find($menu_id);
		if ($info["tab_menu"]) {
			$list = explode("|", $info["tab_menu"]);
		}
		return $list;
	}
}