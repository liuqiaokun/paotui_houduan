<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class WxUpload extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete"])) {
			$idx = $this->request->param("id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\accounts\model\WxUpload::find($v);
					if ($info["wxapp_id"] != session("accounts.wxapp_id")) {
						$this->error("你没有操作权限");
					}
				}
			}
		}
	}
	public function index()
	{
		if (!$this->request->isAjax()) {
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,number,addtime,is_online";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\accounts\service\WxUploadService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "wxapp_id,number,version_desc,addtime";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\WxUploadService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$id = $this->request->get("id", "", "serach_in");
			if (!$id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\accounts\model\WxUpload::find($id)));
			return view("update");
		} else {
			$postField = "id,wxapp_id,number,version_desc";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\WxUploadService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\WxUpload::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function upload()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", session("accounts.wxapp_id"))->find();
		$version = \think\facade\Db::name("wx_upload")->find($idx);
		if (empty($setting["app_up_key"])) {
			return json(["status" => "01", "msg" => "请先上传小程序代码上传密钥"]);
		}
		$data["app_id"] = $setting["appid"];
		$data["wxapp_id"] = session("accounts.wxapp_id");
		$data["version"] = $version["number"];
		$data["app_up_key"] = $setting["app_up_key"];
		$data["version_desc"] = $version["version_desc"];
		return json(["status" => "00", "msg" => "返回成功", "data" => $data]);
	}
	public function updateStatus()
	{
		$id = $this->request->post("id", "", "serach_in");
		if (!$id) {
			$this->error("参数错误");
		}
		$data = \think\facade\Db::name("wx_upload")->find($id);
		if (!$data) {
			if (!$id) {
				$this->error("该条数据不存在");
			}
		}
		\think\facade\Db::name("wx_upload")->where("wxapp_id", session("accounts.wxapp_id"))->update(["is_online" => 0]);
		\think\facade\Db::name("wx_upload")->where("id", $id)->update(["is_online" => 1]);
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function xcxUpload()
	{
		$id = $this->request->post("id", "", "serach_in");
		if (!$id) {
			$this->error("参数错误");
		}
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", session("accounts.wxapp_id"))->find();
		$version = \think\facade\Db::name("wx_upload")->find($id);
		$data["app_id"] = $setting["appid"];
		$data["wxapp_id"] = session("accounts.wxapp_id");
		$data["version"] = $version["number"];
		$data["app_up_key"] = $setting["app_up_key"];
		$data["version_desc"] = $version["version_desc"];
		$data["api_url"] = $_SERVER["HTTP_HOST"];
//		$url = "https://send.fkynet.net/api/MiniAppUp/wxUpBk";
        $url = "";
		$result = HttpExtend::post($url, $data);
		$result = json_decode($result, true);
		return json(["status" => $result["status"], "msg" => $result["msg"]]);
	}
}