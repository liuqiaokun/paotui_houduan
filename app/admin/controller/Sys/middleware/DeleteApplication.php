<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller\Sys\middleware;

class DeleteApplication extends \app\admin\controller\Admin
{
	public function handle($request, \Closure $next)
	{
		$data = $request->param();
		$applicationInfo = \app\admin\controller\Sys\model\Application::find($data["app_id"]);
		$rootPath = app()->getRootPath();
		if ($applicationInfo["app_type"] == 3) {
			if ($applicationInfo["app_dir"]) {
				deldir($rootPath . "/app/" . $applicationInfo["app_dir"]);
			}
			$cmsCount = \app\admin\controller\Sys\model\Application::where(["app_type" => 3])->count();
			if ($cmsCount <= 1) {
				deldir($rootPath . "/app/admin/controller/Cms");
				deldir($rootPath . "/app/admin/view/cms");
				deldir($rootPath . "/app/admin/service/Cms");
				deldir($rootPath . "/app/admin/model/Cms");
				\think\facade\Db::execute("DROP TABLE IF EXISTS " . config("database.connections.mysql.prefix") . "content");
				\think\facade\Db::execute("DROP TABLE IF EXISTS " . config("database.connections.mysql.prefix") . "catagory");
				\think\facade\Db::execute("DROP TABLE IF EXISTS " . config("database.connections.mysql.prefix") . "frament");
				\think\facade\Db::execute("DROP TABLE IF EXISTS " . config("database.connections.mysql.prefix") . "position");
				$extendList = \app\admin\controller\Sys\model\Menu::where(["app_id" => $data["app_id"]])->select();
				if ($extendList) {
					foreach ($extendList as $key => $val) {
						\app\admin\controller\Sys\model\Field::where(["menu_id" => $val["menu_id"]])->delete();
						\app\admin\controller\Sys\model\Menu::where(["menu_id" => $val["menu_id"]])->delete();
						\think\facade\Db::execute("DROP TABLE IF EXISTS " . config("database.connections.mysql.prefix") . $val["table_name"]);
					}
				}
			}
		} else {
			if ($applicationInfo["app_dir"]) {
				deldir($rootPath . "/app/" . $applicationInfo["app_dir"]);
			}
			$where["menu_id"] = \app\admin\controller\Sys\model\Menu::where(["app_id" => $data["app_id"]])->column("menu_id");
			\app\admin\controller\Sys\model\Menu::where(["app_id" => $data["app_id"]])->delete();
			\app\admin\controller\Sys\model\Field::where($where)->delete();
			\app\admin\controller\Sys\model\Action::where($where)->delete();
		}
		return $next($request);
	}
}