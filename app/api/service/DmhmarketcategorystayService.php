<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class DmhmarketcategorystayService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = db("dmh_market_category_stay")->field($field)->alias("a")->join("wechat_user b", "a.u_id=b.u_id", "left")->where($where)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$data["pid"] = !is_null($data["pid"]) ? $data["pid"] : "0";
			$data["create_time"] = time();
			$res = \app\api\model\Dmhmarketcategorystay::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
}