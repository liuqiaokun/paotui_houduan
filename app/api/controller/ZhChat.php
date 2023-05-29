<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhChat extends Common
{
	public function check()
	{
		$wxapp_id = $this->request->post("wxapp_id");
//		$url1 = "http://send.fkynet.net/api/Plugins/is_purchase";
        $url1 = "";
        $res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=1&user_wxappid=" . $wxapp_id);
		$res = json_decode($res, true);
		if ($res["code"] != 200) {
			return $this->ajaxReturn($this->successCode, $res["msg"], htmlOutList($res));
		} else {
			return $this->ajaxReturn($this->successCode, "已授权");
		}
	}
	public function list()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$uId = $this->request->uid;
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = $this->app->db->name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$user = \app\api\model\Member::where(["u_id" => $uId, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$map1 = [["wxapp_id", "=", $wxapp_id], ["status", "=", 1], ["u_id", "=", $uId]];
		$map2 = [["wxapp_id", "=", $wxapp_id], ["status", "=", 1], ["s_id", "=", $uId]];
		$data = \app\api\model\ZhChat::whereOr([$map1, $map2])->select()->toArray();
		foreach ($data as &$v) {
			if ($v["u_id"] != $uId) {
				$oId = $v["u_id"];
			} else {
				$oId = $v["s_id"];
			}
			$memberData = \app\api\model\Member::where(["wxapp_id" => $wxapp_id, "u_id" => $oId])->field("nickname,avatar,u_id")->find();
			$v["chat_user"] = $memberData;
			$v["unread_num"] = \app\api\model\ZhChatLog::where(["wxapp_id" => $wxapp_id, "chat_id" => $v["id"], "u_id" => $oId, "is_read" => 0])->count("id");
			$lastInfo = \app\api\model\ZhChatLog::where(["wxapp_id" => $wxapp_id, "chat_id" => $v["id"]])->order("id desc")->field("type,content,record_time")->find();
			if ($lastInfo && $lastInfo["type"] == "img") {
				$v["last_info"] = "[图片]";
			} elseif ($lastInfo && $lastInfo["type"] == "voice") {
				$v["last_info"] = "[语音]";
			} elseif ($lastInfo && $lastInfo["type"] == "text") {
				$v["last_info"] = $lastInfo["content"];
			} else {
				$v["last_info"] = "";
			}
			$v["record_time"] = $lastInfo["record_time"] ?: "";
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "成功", $data);
	}
	public function getChatRecord()
	{
		$wxapp_id = $this->request->post("wxapp_id");
//		$url1 = "http://send.fkynet.net/api/Plugins/is_purchase";
        $url1 = "";
		$res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=1&user_wxappid=" . $wxapp_id);
		$res = json_decode($res, true);
		if ($res["code"] != 200) {
			return $this->ajaxReturn($this->errorCode, $res["msg"], htmlOutList($res));
		}
		$uId = $this->request->uid;
		$sId = $this->request->post("s_id", "", "serach_in");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = $this->app->db->name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$user = \app\api\model\Member::where(["u_id" => $uId, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$SUser = \app\api\model\Member::where(["u_id" => $sId, "wxapp_id" => $wxapp_id])->find();
		if (!$SUser) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$map1 = [["wxapp_id", "=", $wxapp_id], ["u_id", "=", $uId], ["s_id", "=", $sId]];
		$map2 = [["wxapp_id", "=", $wxapp_id], ["u_id", "=", $sId], ["s_id", "=", $uId]];
		$findData = \app\api\model\ZhChat::whereOr([$map1, $map2])->find();
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$field = "a.*,b.u_id,b.nickname,b.avatar";
		$orderby = "a.id desc";
		$where = ["a.wxapp_id" => $wxapp_id, "a.chat_id" => $findData["id"] ?: 0];
		\app\api\model\ZhChatLog::where(["wxapp_id" => $wxapp_id, "chat_id" => $findData["id"], "is_read" => 0, "u_id" => $sId])->update(["is_read" => 1]);
		$chatLogData = \app\api\model\ZhChatLog::alias("a")->join("wechat_user b ", "b.u_id= a.u_id")->where($where)->field($field)->order($orderby)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		$chatLogData["data"] = array_reverse($chatLogData["data"]);
		foreach ($chatLogData["data"] as &$v) {
			$v = ["type" => "user", "msg" => ["id" => $v["id"], "type" => $v["type"], "time" => date("m-d H:i", strtotime($v["add_time"])), "userinfo" => ["u_id" => $v["u_id"], "username" => $v["nickname"], "face" => $v["avatar"]], "content" => $v["type"] == "text" ? [$v["type"] => $v["content"]] : json_decode($v["content"])]];
		}
		$res["sdata"] = $SUser;
		$res["data"] = $chatLogData;
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function removeChat()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$uId = $this->request->uid;
		$chatId = $this->request->post("chat_id", "", "serach_in");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \app\api\model\SysSite::where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$user = \app\api\model\Member::where(["u_id" => $uId, "wxapp_id" => $wxapp_id])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		if (!$chatId) {
			return $this->ajaxReturn($this->errorCode, "缺少参数");
		}
		\app\api\model\ZhChat::where(["wxapp_id" => $wxapp_id, "id" => $chatId, "status" => 1])->update(["status" => 0]);
		return $this->ajaxReturn($this->successCode, "成功");
	}
}