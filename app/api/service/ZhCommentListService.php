<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class ZhCommentListService extends \xhadmin\CommonService
{
	public static function add($data)
	{
		try {
			validate("app\\api\\validate\\ZhCommentList")->scene("add")->check($data);
			$data["createtime"] = time();
			$res = \app\api\model\ZhCommentList::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
}