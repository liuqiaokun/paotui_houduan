<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class ZhGiveThumbsUpService extends \xhadmin\CommonService
{
	public static function giveUp($data)
	{
		try {
			$res = \app\api\model\ZhGiveThumbsUp::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function forwardAccumulation($data)
	{
		try {
			$res = \app\api\model\ZhGiveThumbsUp::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
}