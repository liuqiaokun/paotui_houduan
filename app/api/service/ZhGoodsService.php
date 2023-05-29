<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class ZhGoodsService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = db("zh_goods")->field($field)->where($where)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			validate("app\\api\\validate\\ZhGoods")->scene("add")->check($data);
			$data["status"] = !is_null($data["status"]) ? $data["status"] : "2";
			$data["createtime"] = time();
			$res = \app\api\model\ZhGoods::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function update($where, $data)
	{
		try {
			validate("app\\api\\validate\\ZhGoods")->scene("update")->check($data);
			!is_null($data["createtime"]) && ($data["createtime"] = strtotime($data["createtime"]));
			$res = \app\api\model\ZhGoods::where($where)->update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
	public static function goodShelves($data)
	{
		try {
			$data["status"] = !is_null($data["status"]) ? $data["status"] : "2";
			$res = \app\api\model\ZhGoods::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
}