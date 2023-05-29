<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Sms extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update"])) {
			$idx = $this->request->param("id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\accounts\model\Sms::find($v);
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
			$count = \think\facade\Db::name("sms")->where("wxapp_id", session("accounts.wxapp_id"))->count();
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
			$field = "id,sms_status,sms_type,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\accounts\service\SmsService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function updateExt()
	{
		$postField = "id,sms_status";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["id"]) {
			$this->error("参数错误");
		}
		try {
			\app\accounts\model\Sms::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "wxapp_id,sms_status,sms_type,ali_sms_accesskeyid,ali_sms_accesskeysecret,ali_sms_signname,ali_sms_tempcode,cl_account,cl_pwd,cl_sign,create_time";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\SmsService::add($data);
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
			$this->view->assign("info", checkData(\app\accounts\model\Sms::find($id)));
			return view("update");
		} else {
			$postField = "id,wxapp_id,sms_status,sms_type,ali_sms_accesskeyid,ali_sms_accesskeysecret,ali_sms_signname,ali_sms_tempcode,cl_account,cl_pwd,cl_sign";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\SmsService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
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