<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Sys\middleware;

class UpTable extends \app\admin\controller\Admin
{
	public function handle($request, \Closure $next)
	{
		$data = $request->param();
		$menuInfo = \app\admin\controller\Sys\model\Menu::find($data["menu_id"]);
		if ($data["table_status"] && $data["table_name"] && $data["pk_id"]) {
			try {
				$data["table_name"] = strtolower(trim($data["table_name"]));
				$data["pk_id"] = strtolower(trim($data["pk_id"]));
				$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
				if (self::getTable($menuInfo["table_name"], $connect)) {
					if ($data["pk_id"] != $menuInfo["pk_id"]) {
						$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "" . $menuInfo["table_name"] . " CHANGE " . $menuInfo["pk_id"] . " " . $data["pk_id"] . " INT( 11 ) COMMENT '编号' NOT NULL AUTO_INCREMENT";
						$res = \think\facade\Db::connect($connect)->execute($sql);
						$where["name"] = "编号";
						$where["menu_id"] = $data["menu_id"];
						db("field")->where($where)->update(["field" => $data["pk_id"]]);
					}
					if ($data["table_name"] && $data["table_name"] != $menuInfo["table_name"]) {
						$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "" . $menuInfo["table_name"] . " RENAME TO " . config("database.connections." . $connect . ".prefix") . "" . $data["table_name"];
						\think\facade\Db::connect($connect)->execute($sql);
					}
				} else {
					$sql = " CREATE TABLE IF NOT EXISTS `" . config("database.connections." . $connect . ".prefix") . "" . $data["table_name"] . "` ( ";
					$sql .= "
						`" . $data["pk_id"] . "` int(10) NOT NULL AUTO_INCREMENT ,
						PRIMARY KEY (`" . $data["pk_id"] . "`)
						) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
					";
					\think\facade\Db::connect($connect)->execute($sql);
					$info = db("field")->where(["name" => "编号", "menu_id" => $data["menu_id"]])->find();
					if (!$info) {
						$defaultData["pk_id"] = $data["pk_id"];
						$defaultData["title"] = $data["title"];
						\app\admin\controller\Sys\service\MenuService::createDefaultAction($defaultData, $data["menu_id"]);
					}
				}
			} catch (\Exception $e) {
				return json(["status" => "01", "msg" => $e->getMessage()]);
			}
		}
		if (!$menuInfo["url"] && !empty($menuInfo["controller_name"]) && $data["controller_name"] != $menuInfo["controller_name"]) {
			$rootPath = app()->getRootPath();
			$applicationInfo = \app\admin\controller\Sys\model\Application::find($menuInfo["app_id"]);
			@unlink($rootPath . "/extend/xhadmin/service/" . $applicationInfo["app_dir"] . "/" . $menuInfo["controller_name"] . "Service.php");
			deldir($rootPath . "/app/" . $applicationInfo["app_dir"] . "/view/" . getViewName($menuInfo["controller_name"]));
			@unlink($rootPath . "/app/" . $applicationInfo["app_dir"] . "/controller/" . $menuInfo["controller_name"] . ".php");
			@unlink("./static/js/" . $applicationInfo["app_dir"] . "/" . $menuInfo["controller_name"] . ".js");
			@unlink($rootPath . "/extend/xhadmin/db/" . $menuInfo["controller_name"] . ".php");
		}
		return $next($request);
	}
	public static function getTable($tableName, $connect)
	{
		$list = \think\facade\Db::connect($connect)->query("show tables");
		foreach ($list as $k => $v) {
			$array[] = $v["Tables_in_" . config("database.connections." . $connect . ".database")];
		}
		if (in_array(config("database.connections." . $connect . ".prefix") . $tableName, $array)) {
			return true;
		}
	}
}