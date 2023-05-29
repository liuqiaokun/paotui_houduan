<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\service;

class DatesService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("dates")->field($field)->alias("a")->join("tickets b", "a.t_id=b.t_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function add($data)
	{
		try {
			$data["createtime"] = time();
			$res = \app\gcadmin\model\Dates::create($data);
		} catch (\think\exception\ValidateException $e) {
			throw new \think\exception\ValidateException($e->getError());
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		if (!$res) {
			throw new \think\exception\ValidateException("操作失败");
		}
		return $res->d_id;
	}
}