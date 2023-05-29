<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class ZhGoodsService extends \xhadmin\CommonService
{
	public static function add($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhGoods")->scene("add")->check($data);
			$data["createtime"] = time();
			$res = \app\subschool\model\ZhGoods::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->id;
	}
	public static function update($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhGoods")->scene("update")->check($data);
			$data["createtime"] = strtotime($data["createtime"]);
			$res = \app\subschool\model\ZhGoods::update($data);
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