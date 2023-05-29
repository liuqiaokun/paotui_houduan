<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhInfo extends Common
{
	public function detail()
	{
		$wxapp_id = $this->request->get("wxapp_id");
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
		$data["info_id"] = $this->request->get("info_id", "", "serach_in");
		$data["wxapp_id"] = $wxapp_id;
		$data["s_id"] = $s_id;
		$field = "info_id,title,address,u_id,type,media_type,createtime,phone,pick_date,claim_method,remarks,wxapp_id,image,video";
		$res = checkData(\app\api\model\ZhInfo::field($field)->where($data)->find());
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $res["u_id"], "wxapp_id" => $res["wxapp_id"]])->find();
		$res["nickname"] = $user["nickname"];
		$res["avatar"] = $user["avatar"];
		$res["createtime"] = date("Y-m-d H:i", $res["createtime"]);
		if ($res["media_type"] == 2) {
			$res["image"] = explode(",", $res["image"]);
		}
		$commentData = [];
		$commentListData = \think\facade\Db::name("zh_comment_list")->where(["info_id" => $res["info_id"], "wxapp_id" => $wxapp_id, "s_id" => $s_id, "p_id" => 0])->select()->toArray();
		foreach ($commentListData as &$value) {
			$userData = \think\facade\Db::name("wechat_user")->where(["u_id" => $value["u_id"], "wxapp_id" => $wxapp_id])->find();
			$value["nickname"] = $userData["nickname"];
			$value["avatar"] = $userData["avatar"];
			$time = new \utils\Time();
			$value["createtime"] = $time->timeDiff($value["createtime"]);
			$a = $this->commentRecursion($value["id"], $value["u_id"], "gc_zh_comment_list");
			$value["child"] = $a;
			$value["block"] = false;
		}
		$res["comment"] = $commentListData;
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
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
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where(["u_id" => $uid, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知的用户");
		}
		$postField = "title,address,u_id,type,media_type,createtime,phone,pick_date,claim_method,remarks,image,video";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["s_id"] = $s_id;
		$data["wxapp_id"] = $wxapp_id;
		$data["u_id"] = $uid;
		$data["createtime"] = time();
		if ($data["image"] && !$data["video"]) {
			$data["media_type"] = 2;
		} else {
			if (!$data["image"] && $data["video"]) {
				$data["media_type"] = 1;
			}
		}
		$judge = true;
		$judge1 = true;
		$judge2 = true;
		$judge3 = true;
		$judge4 = true;
		if ($data["title"]) {
			$judge1 = $this->msg_check($data["title"], $data["wxapp_id"]);
		}
		if ($data["address"]) {
			$judge2 = $this->msg_check($data["address"], $data["wxapp_id"]);
		}
		if ($data["claim_method"]) {
			$judge3 = $this->msg_check($data["claim_method"], $data["wxapp_id"]);
		}
		if ($data["pick_date"]) {
			$judge4 = $this->msg_check($data["pick_date"], $data["wxapp_id"]);
		}
		if ($data["remarks"]) {
			$judge = $this->msg_check($data["remarks"], $data["wxapp_id"]);
		}
		if (!$judge || !$judge1 || !$judge2 || !$judge3 || !$judge4) {
			return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
		}
		$res = \app\api\service\ZhInfoService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function getInformationList()
	{
		if (!$this->request->isGet()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$wxapp_id = $this->request->get("wxapp_id");
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
		$where["type"] = $this->request->get("type", "", "serach_in");
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$field = "info_id,title,address,u_id,type,media_type,createtime,phone,pick_date,claim_method,remarks,image,video";
		$orderby = "info_id desc";
		$res = \app\api\service\ZhInfoService::getInformationListList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as &$v) {
			$nickName = \think\facade\Db::name("wechat_user")->where("u_id", $v["u_id"])->find();
			$v["nickname"] = $nickName["nickname"];
			$v["avatar"] = $nickName["avatar"];
			if ($v["media_type"] == 2) {
				$v["image"] = explode(",", $v["image"]);
			}
			$v["createtime"] = date("Y-m-d H:i", $v["createtime"]);
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function getMyInformationList()
	{
		if (!$this->request->isGet()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		$type = $this->request->get("type");
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
		$where["u_id"] = $this->request->uid;
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$where["type"] = $type;
		$field = "info_id,title,address,u_id,type,phone,pick_date,claim_method,remarks,image,video,media_type,createtime";
		$orderby = "info_id desc";
		$res = \app\api\service\ZhInfoService::getMyInformationListList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as &$v) {
			$nickName = \think\facade\Db::name("wechat_user")->where("u_id", $v["u_id"])->find();
			$v["nickname"] = $nickName["nickname"];
			$v["avatar"] = $nickName["avatar"];
			if ($v["media_type"] == 2) {
				$v["image"] = explode(",", $v["image"]);
			}
			$v["createtime"] = date("Y-m-d H:i:s", $v["createtime"]);
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
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
		$idx = $this->request->post("info_id", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["info_id"] = $idx;
		$data["wxapp_id"] = $wxapp_id;
		$data["s_id"] = $s_id;
		try {
			$r = \app\api\model\ZhInfo::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function commentRecursion($pid, $uid, $table)
	{
		$result = [];
		$data = \think\facade\Db::table($table)->alias("g")->leftJoin("wechat_user b", "b.u_id = g.u_id")->where("g.p_id", $pid)->field("g.*,b.nickname,b.avatar")->select();
		$p_user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		foreach ($data as &$v) {
			$time = new \utils\Time();
			$v["createtime"] = $time->timeDiff($v["createtime"]);
			$v["content"] = "回复@" . $p_user["nickname"] . ":" . $v["content"];
			$result[] = $v;
			$result = array_merge($result, $this->commentRecursion($v["id"], $v["u_id"], $table));
		}
		return $result;
	}
	public function searchInfo()
	{
		$wxapp_id = $this->request->get("wxapp_id");
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
		$kwd = $this->request->get("kwd", "", "serach_in");
		if (empty($kwd)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["title"] = ["like", "%" . $kwd . "%"];
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$field = "*";
		$orderby = "info_id desc";
		$res = \app\api\service\ZhInfoService::getInformationListList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as &$v) {
			$nickName = \think\facade\Db::name("wechat_user")->where("u_id", $v["u_id"])->find();
			$v["nickname"] = $nickName["nickname"];
			$v["avatar"] = $nickName["avatar"];
			if ($v["media_type"] == 2) {
				$v["image"] = explode(",", $v["image"]);
			}
			$v["createtime"] = date("Y-m-d H:i:s", $v["createtime"]);
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}