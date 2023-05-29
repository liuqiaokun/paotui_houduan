<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class ZhGoodsTypeService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = db("zh_goods_type")->field($field)->where($where)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\api\\validate\\ZhGoodsType")->scene("add")->check($data);
			$data["createtime"] = time();
			$res = \app\api\model\ZhGoodsType::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->goods_type_id;
	}
	public static function update($where, $data)
	{
		try {
			validate("app\\api\\validate\\ZhGoodsType")->scene("update")->check($data);
			!is_null($data["createtime"]) && ($data["createtime"] = strtotime($data["createtime"]));
			$res = \app\api\model\ZhGoodsType::where($where)->update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
}