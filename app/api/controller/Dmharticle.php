<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmharticle extends Common
{
	public function index()
	{
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["title"] = $this->request->get("title", "", "serach_in");
		$createtime_start = $this->request->get("createtime_start", "", "serach_in");
		$createtime_end = $this->request->get("createtime_end", "", "serach_in");
		$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\DmharticleService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$postField = "title,content,createtime,wxapp_id,s_id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$res = \app\api\service\DmharticleService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function update()
	{
		$postField = "id,title,content,createtime,wxapp_id,s_id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (empty($data["id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["id"] = $data["id"];
		$res = \app\api\service\DmharticleService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function delete()
	{
		$idx = $this->request->post("ids", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["id"] = explode(",", $idx);
		try {
			\app\api\model\Dmharticle::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function view()
	{
		$data["id"] = $this->request->post("id", "", "serach_in");
		$field = "id,title,content,createtime,wxapp_id,s_id";
		$res = checkData(\app\api\model\Dmharticle::field($field)->where($data)->find());
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
}