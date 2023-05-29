<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class DmhfootprintService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = db("dmh_footprint")->field($field)->alias("a")->join("dmh_market_goods b", "a.m_id=b.m_id", "left")->where($where)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$res = \app\api\model\Dmhfootprint::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
}