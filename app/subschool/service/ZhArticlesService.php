<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\service;

class ZhArticlesService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = db("zh_articles")->field($field)->alias("a")->join("zh_forum_class b", "a.class_id=b.class_id", "left")->where($where)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function update($data)
	{
		try {
			validate("app\\subschool\\validate\\ZhArticles")->scene("update")->check($data);
			$data["createtime"] = strtotime($data["createtime"]);
			$res = \app\subschool\model\ZhArticles::update($data);
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
	public static function hszList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\subschool\model\ZhArticles::onlyTrashed()->where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
}