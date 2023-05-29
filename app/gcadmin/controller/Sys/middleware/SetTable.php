<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys\middleware;

class SetTable extends \app\gcadmin\controller\Admin
{
	public function handle($request, \Closure $next)
	{
		$data = $request->param();
		if ($data["table_status"] && $data["table_name"] && $data["pk_id"]) {
			try {
				$data["table_name"] = strtolower(trim($data["table_name"]));
				$data["pk_id"] = strtolower(trim($data["pk_id"]));
				$connect = $data["connect"] ? $data["connect"] : config("database.default");
				$sql = " CREATE TABLE IF NOT EXISTS `" . config("database.connections." . $connect . ".prefix") . "" . $data["table_name"] . "` ( ";
				$sql .= "
					`" . $data["pk_id"] . "` int(11) NOT NULL AUTO_INCREMENT ,
					PRIMARY KEY (`" . $data["pk_id"] . "`)
					) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
				";
				\think\facade\Db::connect($connect)->execute($sql);
				$applicationInfo = \app\gcadmin\controller\Sys\model\Application::find($data["app_id"]);
				if ($applicationInfo["app_type"] == 3) {
					$propertyField = \app\gcadmin\controller\Sys\service\FieldSetService::propertyField();
					$property = $propertyField[2];
					$sql = "ALTER TABLE " . config("database.connections." . $connect . ".prefix") . "{$data["table_name"]} ADD content_id {$property["name"]}({$property["maxlen"]}{$property["decimal"]}) DEFAULT NULL";
				}
				\think\facade\Db::connect($connect)->execute($sql);
			} catch (\Exception $e) {
				return json(["status" => "01", "msg" => $e->getMessage()]);
			}
		}
		return $next($request);
	}
}