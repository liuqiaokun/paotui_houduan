<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\service\Cms;

class FramentService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\gcadmin\model\Cms\Frament::where($where)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page]);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res->items(), "total" => $res->total()];
	}
	public static function add($data)
	{
		if (empty($data["title"])) {
			throw new \think\exception\ValidateException("碎片名称不能为空");
		}
		try {
			$res = \app\gcadmin\model\Cms\Frament::create($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function update($data)
	{
		if (empty($data["title"])) {
			throw new \think\exception\ValidateException("碎片名称不能为空");
		}
		try {
			$res = \app\gcadmin\model\Cms\Frament::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
}