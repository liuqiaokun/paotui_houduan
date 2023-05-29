<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Address extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$where["u_id"] = $this->request->uid;
		$field = "*";
		$orderby = "a_id desc";
		$res = \app\api\service\AddressService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$postField = "wxapp_id,u_id,name,sex,phone,s_id,addres,create_time";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$res = \app\api\service\AddressService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function update()
	{
		$postField = "a_id,wxapp_id,u_id,name,sex,phone,s_id,addres";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		if (empty($data["a_id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["a_id"] = $data["a_id"];
		$where["u_id"] = $data["u_id"];
		$res = \app\api\service\AddressService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function delete()
	{
		$idx = $this->request->post("a_ids", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["u_id"] = $this->request->uid;
		$data["a_id"] = explode(",", $idx);
		try {
			\app\api\model\Address::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function view()
	{
		$data["u_id"] = $this->request->uid;
		$data["a_id"] = $this->request->post("a_id", "", "serach_in");
		$field = "a_id,wxapp_id,u_id,name,sex,phone,s_id,addres,create_time";
		$res = checkData(\app\api\model\Address::field($field)->where($data)->find());
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
}