<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Dmhschoolcarry extends Admin
{
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
			$where["alipay_name"] = $this->request->param("alipay_name", "", "serach_in");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,s_id,state,pay,alipay_name,alipay_account,create_time";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$school = \think\facade\Db::name("school")->where("wxapp_id", session("accounts.wxapp_id"))->select();
			foreach ($school as &$v) {
				$schoolList[$v["s_id"]] = $v["s_name"];
			}
			$res = \app\accounts\service\DmhschoolcarryService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			foreach ($res["rows"] as &$v) {
				$v["s_name"] = $schoolList[$v["s_id"]];
			}
			return json($res);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$id = $this->request->get("id", "", "serach_in");
			if (!$id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\accounts\model\Dmhschoolcarry::find($id)));
			return view("update");
		} else {
			$postField = "id,state,pay,alipay_name,alipay_account";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\accounts\service\DmhschoolcarryService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
}