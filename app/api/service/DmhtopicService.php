<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class DmhtopicService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $title, $page)
	{
		try {
			$res = \app\api\model\Dmhtopic::where($where)->where("title", "like", "%" . $title . "%")->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
}