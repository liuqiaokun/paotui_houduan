<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class UserWithdrawService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\UserWithdraw::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			if ($data["type"] == 1) {
				validate("app\\api\\validate\\UserWithdraw")->scene("add")->check($data);
			} else {
				validate("app\\api\\validate\\UserWithdraw")->scene("wx")->check($data);
			}
			$data["status"] = !is_null($data["status"]) ? $data["status"] : "1";
			$data["create_time"] = time();
			$res = \app\api\model\UserWithdraw::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
}