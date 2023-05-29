<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class WechatUserService extends \xhadmin\CommonService
{
	public static function update($where, $data)
	{
		try {
			validate("app\\api\\validate\\WechatUser")->scene("update")->check($data);
			$res = \app\api\model\WechatUser::where($where)->update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
}