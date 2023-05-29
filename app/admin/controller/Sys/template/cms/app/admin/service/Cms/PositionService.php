<?php

//decode by http://www.yunlu99.com/
namespace app\admin\service\Cms;

class PositionService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $orderby, $limit, $page)
	{
		try {
			$res = \app\admin\model\Cms\Position::where($where)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page]);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res->items(), "total" => $res->total()];
	}
	public static function add($data)
	{
		if (empty($data["title"])) {
			throw new \think\exception\ValidateException("名称不能为空");
		}
		try {
			$res = \app\admin\model\Cms\Position::create($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res->id;
	}
	public static function update($data)
	{
		if (empty($data["title"])) {
			throw new \think\exception\ValidateException("名称不能为空");
		}
		try {
			$res = \app\admin\model\Cms\Position::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $res;
	}
}