<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhCommenes extends Common
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
		$articleId = $this->request->post("article_id");
		if (!$articleId) {
			return $this->ajaxReturn($this->errorCode, "缺少信息参数");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$pid = $this->request->post("p_id") ?: 0;
		$postField = "contents";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["wxapp_id"] = $wxapp_id;
		$data["s_id"] = $s_id;
		$data["article_id"] = $articleId;
		$data["u_id"] = $uid;
		$data["p_id"] = $pid;
		$data["createtime"] = time();
		$judge = $this->msg_check($data["contents"], $data["wxapp_id"]);
		if (!$judge) {
			return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
		}
		$article = \think\facade\Db::name("zh_articles")->where("article_id", $articleId)->find();
		$res = \app\api\service\ZhCommenesService::add($data);
		if ($res) {
			\think\facade\Db::name("zh_articles")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "article_id" => $articleId])->inc("comments_num")->update();
		}
		MsgSubscribe::toCommentator($data["contents"], $article["u_id"], $wxapp_id, $articleId);
		MpMsgSubscribe::toCommentator($data["contents"], $article["u_id"], $wxapp_id, $articleId);
		if ($pid > 0) {
			$com = \think\facade\Db::name("zh_commenes")->find($pid);
			MsgSubscribe::toCommentator($data["contents"], $com["u_id"], $wxapp_id, $articleId);
			MpMsgSubscribe::toCommentator($data["contents"], $com["u_id"], $wxapp_id, $articleId);
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function getCommentCount($pid)
	{
		$result = [];
		$data = \think\facade\Db::name("zh_commenes")->alias("g")->where("g.p_id", $pid)->select();
		foreach ($data as &$v) {
			$result[] = $v;
			$result = array_merge($result, $this->getCommentCount($v["id"]));
		}
		return $result;
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
		$info = \think\facade\Db::name("zh_commenes")->find($idx);
		$count = count($this->getCommentCount($idx));
		\think\facade\Db::name("zh_articles")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "article_id" => $info["article_id"]])->dec("comments_num", $count + 1)->update();
		try {
			\app\api\model\ZhCommenes::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
}