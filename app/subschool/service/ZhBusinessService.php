<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class ZhBusinessService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("zh_business")->field($field)->alias("a")->join("zh_business_type b", "a.type_id=b.type_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhBusiness")->scene("add")->check($data);
			$data["s_id"] = session("subschool.s_id");
			$data["wxapp_id"] = session("subschool.wxapp_id");
			$data["expire_time"] = strtotime($data["expire_time"]);
			$data["createtime"] = time();
			$res = \app\subschool\model\ZhBusiness::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->business_id;
	}
	public static function update($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhBusiness")->scene("update")->check($data);
			$data["expire_time"] = strtotime($data["expire_time"]);
			$res = \app\subschool\model\ZhBusiness::update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res;
	}
}