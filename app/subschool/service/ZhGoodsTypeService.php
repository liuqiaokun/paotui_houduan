<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class ZhGoodsTypeService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("zh_goods_type")->field($field)->alias("a")->join("zh_business b", "a.business_id=b.business_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhGoodsType")->scene("add")->check($data);
			$data["createtime"] = time();
			$res = \app\subschool\model\ZhGoodsType::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->goods_type_id;
	}
	public static function update($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhGoodsType")->scene("update")->check($data);
			$data["createtime"] = strtotime($data["createtime"]);
			$res = \app\subschool\model\ZhGoodsType::update($data);
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