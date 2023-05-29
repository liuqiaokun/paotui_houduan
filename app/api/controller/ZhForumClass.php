<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhForumClass extends Common
{
	public function index()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		$type = $this->request->get("type");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$createtime_start = $this->request->get("createtime_start", "", "serach_in");
		$createtime_end = $this->request->get("createtime_end", "", "serach_in");
		$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		if ($type == 1) {
			$where["is_cate"] = 1;
		}
		$field = "*";
		$orderby = "sort desc";
		$res = \app\api\service\ZhForumClassService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function view()
	{
		$data["class_id"] = $this->request->post("class_id");
		$field = "*";
		$res = checkData(\app\api\model\ZhForumClass::field($field)->where($data)->find());
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
}