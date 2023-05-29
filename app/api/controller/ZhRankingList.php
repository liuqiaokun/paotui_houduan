<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhRankingList extends Common
{
	public function index()
	{
		$token = \think\facade\Request::header("Authorization");
		if ($token) {
			$jwt = Jwt::getInstance();
			$jwt->setIss(config("my.jwt_iss"))->setAud(config("my.jwt_aud"))->setSecrect(config("my.jwt_secrect"))->setToken($token);
			if ($jwt->decode()->getClaim("exp") < time()) {
				return json(["status" => config("my.jwtExpireCode"), "msg" => "token过期"]);
			}
			if ($jwt->validate() && $jwt->verify()) {
				$uid = $jwt->decode()->getClaim("uid");
			}
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
		$limit = $this->request->get("limit", 10, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["auth_sid"] = $s_id;
		$where["wxapp_id"] = $wxapp_id;
		$where["brokerage"] = [">", 0];
		$field = "*";
		$orderby = "brokerage desc";
		$res = \app\api\service\ZhRankingListService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as $k => $v) {
			if ($v["u_id"] == $uid) {
				$v["ranks"] = $k + 1;
				$data["my"] = $v;
			}
		}
		$data["list"] = $res["list"];
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($data));
	}
}