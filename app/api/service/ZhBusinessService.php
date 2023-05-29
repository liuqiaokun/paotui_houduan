<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class ZhBusinessService extends \xhadmin\CommonService
{
	public static function update($where, $data)
	{
		try {
			validate("app\\api\\validate\\ZhBusiness")->scene("update")->check($data);
			!is_null($data["createtime"]) && ($data["createtime"] = strtotime($data["createtime"]));
			$res = \app\api\model\ZhBusiness::where($where)->update($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
	public static function businessOrderListList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\api\model\ZhBusiness::where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["list" => $res["data"], "count" => $res["total"]];
	}
}