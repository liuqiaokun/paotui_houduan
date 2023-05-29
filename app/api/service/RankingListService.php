<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class RankingListService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = db("wechat_user")->field($field)->alias("a")->join("school b", "a.s_id=b.s_id", "left")->where($where)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
}