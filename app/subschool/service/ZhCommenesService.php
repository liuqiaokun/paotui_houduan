<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class ZhCommenesService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("zh_commenes")->field($field)->alias("a")->join("zh_articles b", "a.article_id=b.article_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
}