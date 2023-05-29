<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class DmhmarketgoodsService extends \xhadmin\CommonService
{
	public static function add($data)
	{
		try {
			$data["create_time"] = time();
			$res = \app\api\model\Dmhmarketgoods::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->m_id;
	}
	public static function update($where, $data)
	{
		try {
			!is_null($data["create_time"]) && ($data["create_time"] = strtotime($data["create_time"]));
			$res = \app\api\model\Dmhmarketgoods::where($where)->update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
	public static function indexList($where, $field, $orderby, $limit, $page, $keywords, $sotrvalue)
	{
		try {
			if ($sotrvalue == "最热") {
				$res = db("dmh_market_goods", "mysql")->field($field)->alias("a")->where("a.title", "like", "%" . $keywords . "%")->join("dmh_goods_stay c", "a.m_id=c.m_id", "left")->where($where)->group("a.m_id")->order("con desc")->paginate(["list_rows" => $limit, "page" => $page])->toArray();
			} else {
				$res = db("dmh_market_goods", "mysql")->field($field)->alias("a")->where("a.title", "like", "%" . $keywords . "%")->join("wechat_user b", "a.u_id=b.u_id", "left")->order($orderby)->where($where)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
			}
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
}