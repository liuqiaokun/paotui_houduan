<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class UserFollow extends Common
{
	public function myFollow()
	{
		$u_id = $this->request->uid;
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$page = $this->request->post("page", 1);
		$list = \think\facade\Db::name("user_follow")->alias("f")->join("wechat_user u", "f.f_uid = u.u_id")->where("f.status", 1)->where("f.uid", $u_id)->where("f.wxapp_id", $wxapp_id)->field("f.*,u.avatar,u.nickname")->select();
		foreach ($list as $k => $v) {
			$data = \think\facade\Db::name("user_follow")->where("f_uid", $u_id)->where("wxapp_id", $wxapp_id)->where("uid", $v["f_uid"])->where("status", 1)->find();
			$v["is_mutual"] = $data ? 1 : 0;
			$list[$k] = $v;
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $list);
	}
	public function follow()
	{
		$u_id = $this->request->uid;
		$wxapp_id = $this->request->post("wxapp_id");
		$followed_id = $this->request->post("u_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		if (!$followed_id) {
			return $this->ajaxReturn($this->errorCode, "请先选择用户");
		}
		$record = \think\facade\Db::name("user_follow")->where("uid", $u_id)->where("f_uid", $followed_id)->where("wxapp_id", $wxapp_id)->find();
		if (empty($record)) {
			$res = \think\facade\Db::name("user_follow")->insert(["wxapp_id" => $wxapp_id, "f_uid" => $followed_id, "uid" => $u_id, "status" => 1, "addtime" => date("Y-m-d H:i:s")]);
		} else {
			$res = \think\facade\Db::name("user_follow")->where("id", $record["id"])->update(["status" => !$record["status"]]);
		}
		if ($res) {
			return $this->ajaxReturn($this->successCode, "操作成功", $res);
		} else {
			return $this->ajaxReturn($this->errorCode, "操作失败", $res);
		}
	}
	public function myFans()
	{
		$u_id = $this->request->uid;
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$list = \think\facade\Db::name("user_follow")->alias("f")->join("wechat_user u", "f.uid = u.u_id")->where("f.status", 1)->where("f.f_uid", $u_id)->where("f.wxapp_id", $wxapp_id)->field("f.*,u.avatar,u.nickname")->select();
		foreach ($list as $k => $v) {
			$data = \think\facade\Db::name("user_follow")->where("f_uid", $v["uid"])->where("wxapp_id", $wxapp_id)->where("uid", $u_id)->where("status", 1)->find();
			$v["is_mutual"] = $data ? 1 : 0;
			$list[$k] = $v;
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $list);
	}
	public function commentFav()
	{
		$u_id = $this->request->uid;
		$wxapp_id = $this->request->post("wxapp_id");
		$s_id = $this->request->post("s_id");
		$c_id = $this->request->post("c_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		if (!$c_id) {
			return $this->ajaxReturn($this->errorCode, "请先选择评论");
		}
		$record = \think\facade\Db::name("zh_forum_comment_fav")->where("uid", $u_id)->where("c_id", $c_id)->where("wxapp_id", $wxapp_id)->find();
		if (empty($record)) {
			$res = \think\facade\Db::name("zh_forum_comment_fav")->insert(["wxapp_id" => $wxapp_id, "c_id" => $c_id, "uid" => $u_id, "status" => 1, "s_id" => $s_id, "addtime" => date("Y-m-d H:i:s")]);
		} else {
			$res = \think\facade\Db::name("zh_forum_comment_fav")->where("id", $record["id"])->update(["status" => !$record["status"]]);
		}
		if ($res) {
			return $this->ajaxReturn($this->successCode, "操作成功", $res);
		} else {
			return $this->ajaxReturn($this->errorCode, "操作失败", $res);
		}
	}
}