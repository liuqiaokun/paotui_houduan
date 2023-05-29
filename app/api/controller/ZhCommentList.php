<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhCommentList extends Common
{
	public function add()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$info_id = $this->request->post("info_id");
		if (!$info_id) {
			return $this->ajaxReturn($this->errorCode, "缺少信息参数");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		$postField = "info_id,content";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["wxapp_id"] = $wxapp_id;
		$data["s_id"] = $s_id;
		$data["info_id"] = $info_id;
		$data["u_id"] = $uid;
		$data["p_id"] = $this->request->post("p_id") ?: 0;
		$data["createtime"] = time();
		$judge = $this->msg_check($data["content"], $data["wxapp_id"]);
		if (!$judge) {
			return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
		}
		$res = \app\api\service\ZhCommentListService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function delete()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$idx = $this->request->post("id", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["id"] = $idx;
		$data["wxapp_id"] = $wxapp_id;
		$data["s_id"] = $s_id;
		try {
			\app\api\model\ZhCommentList::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
}