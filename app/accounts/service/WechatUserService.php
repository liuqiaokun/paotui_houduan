<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\service;

class WechatUserService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("wechat_user")->field($field)->alias("a")->join("school b", "a.s_id=b.s_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function update($data)
	{
		try {
			validate("app\\accounts\\validate\\WechatUser")->scene("update")->check($data);
			$res = \app\accounts\model\WechatUser::update($data);
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