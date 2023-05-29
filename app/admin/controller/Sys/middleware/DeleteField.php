<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Sys\middleware;

class DeleteField extends \app\admin\controller\Admin
{
	public function handle($request, \Closure $next)
	{
		$data = $request->param();
		try {
			$fieldInfo = \app\admin\controller\Sys\model\Field::find($data["id"]);
			$menuInfo = \app\admin\controller\Sys\model\Menu::find($fieldInfo["menu_id"]);
			$applicationInfo = \app\admin\controller\Sys\model\Application::find($menuInfo["app_id"]);
			$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
			if ($menuInfo["menu_id"] != config("my.config_module_id")) {
				if ($fieldInfo["is_field"] == 1 && in_array($applicationInfo["app_type"], [1, 3])) {
					foreach (explode("|", $fieldInfo["field"]) as $k => $v) {
						if (self::getFieldStatus(config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"], $v, $connect)) {
							$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"] . " DROP " . $v;
							\think\facade\Db::connect($connect)->execute($sql);
						}
					}
				}
			} else {
				db("config")->where(["name" => $fieldInfo["field"]])->delete();
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
		return $next($request);
	}
	public static function getFieldStatus($tablename, $field, $connect)
	{
		$list = \think\facade\Db::connect($connect)->query("show columns from " . $tablename);
		foreach ($list as $v) {
			$arr[] = $v["Field"];
		}
		if (in_array($field, $arr)) {
			return true;
		}
	}
}