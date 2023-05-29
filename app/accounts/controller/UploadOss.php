<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class UploadOss extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["update", "updateExt"])) {
			$idx = $this->request->param("u_id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\accounts\model\UploadOss::find($v);
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
			$count = \think\facade\Db::name("upload_oss")->where("wxapp_id", session("accounts.wxapp_id"))->count();
			$this->view->assign("count", $count);
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "u_id,oss_status,oss_default_type,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "u_id desc";
			$res = \app\accounts\service\UploadOssService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "wxapp_id,oss_status,oss_default_type,ali_oss_accesskeyid,ali_oss_accesskeysecret,ali_oss_endpoint,ali_oss_bucket,qny_oss_accesskey,qny_oss_secretkey,qny_oss_bucket,qny_oss_domain,create_time,tencent_oss_secretid,tencent_oss_secretkey,tencent_oss_bucket,tencent_oss_region";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\UploadOssService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$u_id = $this->request->get("u_id", "", "serach_in");
			if (!$u_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\accounts\model\UploadOss::find($u_id)));
			return view("update");
		} else {
			$postField = "u_id,wxapp_id,oss_status,oss_default_type,ali_oss_accesskeyid,ali_oss_accesskeysecret,ali_oss_endpoint,ali_oss_bucket,qny_oss_accesskey,qny_oss_secretkey,qny_oss_bucket,qny_oss_domain,create_time,tencent_oss_secretid,tencent_oss_secretkey,tencent_oss_bucket,tencent_oss_region";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\UploadOssService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function updateExt()
	{
		$postField = "u_id,oss_status";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["u_id"]) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\UploadOss::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function indexmodify()
	{
		if (!$this->request->isAjax()) {
			$count = \think\facade\Db::name("setting")->where("wxapp_id", session("accounts.wxapp_id"))->count();
			$this->view->assign("count", $count);
			return view("index");
		}
	}
}