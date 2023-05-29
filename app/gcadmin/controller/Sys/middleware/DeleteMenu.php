<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys\middleware;

class DeleteMenu extends \app\gcadmin\controller\Admin
{
	public function handle($request, \Closure $next)
	{
		$data = $request->param();
		if (in_array($data["menu_id"], [18, 19, 41])) {
			return json(["status" => "01", "msg" => "系统模块禁止卸载"]);
		}
		$menuInfo = \app\gcadmin\controller\Sys\model\Menu::find($data["menu_id"]);
		$applicationInfo = \app\gcadmin\controller\Sys\model\Application::find($menuInfo["app_id"]);
		$where["menu_id"] = $data["menu_id"];
		db("field")->where($where)->delete();
		db("action")->where($where)->delete();
		$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
		if (!empty($menuInfo["table_name"]) && in_array($applicationInfo["app_type"], [1, 3]) && $menuInfo["table_status"]) {
			$sql = \think\facade\Db::connect($connect)->execute("DROP TABLE if exists " . config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"]);
		}
		if (!$menuInfo["url"] && !empty($menuInfo["controller_name"])) {
			$rootPath = app()->getRootPath();
			deldir($rootPath . "/app/" . $applicationInfo["app_dir"] . "/view/" . getViewName($menuInfo["controller_name"]));
			if ($this->getSubFiles($rootPath . "/app/" . $applicationInfo["app_dir"] . "/view/" . explode("/", $menuInfo["controller_name"])[0])) {
				deldir($rootPath . "/app/" . $applicationInfo["app_dir"] . "/view/" . explode("/", $menuInfo["controller_name"])[0]);
			}
			@unlink($rootPath . "/app/" . $applicationInfo["app_dir"] . "/controller/" . $menuInfo["controller_name"] . ".php");
			if ($this->getSubFiles($rootPath . "/app/" . $applicationInfo["app_dir"] . "/controller/" . explode("/", $menuInfo["controller_name"])[0])) {
				deldir($rootPath . "/app/" . $applicationInfo["app_dir"] . "/controller/" . explode("/", $menuInfo["controller_name"])[0]);
			}
			@unlink($rootPath . "/app/" . $applicationInfo["app_dir"] . "/model/" . $menuInfo["controller_name"] . ".php");
			if ($this->getSubFiles($rootPath . "/app/" . $applicationInfo["app_dir"] . "/model/" . explode("/", $menuInfo["controller_name"])[0])) {
				deldir($rootPath . "/app/" . $applicationInfo["app_dir"] . "/model/" . explode("/", $menuInfo["controller_name"])[0]);
			}
			@unlink($rootPath . "/app/" . $applicationInfo["app_dir"] . "/service/" . $menuInfo["controller_name"] . "Service.php");
			if ($this->getSubFiles($rootPath . "/app/" . $applicationInfo["app_dir"] . "/service/" . explode("/", $menuInfo["controller_name"])[0])) {
				deldir($rootPath . "/app/" . $applicationInfo["app_dir"] . "/service/" . explode("/", $menuInfo["controller_name"])[0]);
			}
			@unlink($rootPath . "/app/" . $applicationInfo["app_dir"] . "/validate/" . $menuInfo["controller_name"] . ".php");
			if ($this->getSubFiles($rootPath . "/app/" . $applicationInfo["app_dir"] . "/validate/" . explode("/", $menuInfo["controller_name"])[0])) {
				deldir($rootPath . "/app/" . $applicationInfo["app_dir"] . "/validate/" . explode("/", $menuInfo["controller_name"])[0]);
			}
		}
		return $next($request);
	}
	private function getSubFiles($filepath)
	{
		$res = scandir($filepath);
		if (count($res) == 2) {
			return true;
		}
	}
}