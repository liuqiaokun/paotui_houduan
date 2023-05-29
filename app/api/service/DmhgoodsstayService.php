<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class DmhgoodsstayService extends \xhadmin\CommonService
{
	public static function add($data)
	{
		try {
			$res = \app\api\model\Dmhgoodsstay::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function indexList($where, $field, $orderby, $limit, $page, $uid, $s_id, $wxapp_id)
	{
		try {
			$res = db("dmh_goods_stay", "mysql")->where("a.u_id", $uid)->where("a.s_id", $s_id)->where("a.wxapp_id", $wxapp_id)->field($field)->alias("a")->join("dmh_market_goods b", "a.m_id=b.m_id", "left")->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
}