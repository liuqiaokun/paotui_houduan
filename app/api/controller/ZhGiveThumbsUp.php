<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhGiveThumbsUp extends Common
{
	public function giveUp()
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
		$articlesId = $this->request->post("article_id");
		if (!$articlesId) {
			return $this->ajaxReturn($this->errorCode, "缺少文章参数");
		}
		$zhGiveThumbsUp = \think\facade\Db::name("zh_give_thumbs_up")->where(["articles_id" => $articlesId, "u_id" => $uid])->find();
		$articleData = \think\facade\Db::name("zh_articles")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "article_id" => $articlesId])->find();
		if ($zhGiveThumbsUp && $zhGiveThumbsUp["status"] == 0) {
			$results = \think\facade\Db::name("zh_give_thumbs_up")->where(["articles_id" => $articlesId, "u_id" => $uid, "id" => $zhGiveThumbsUp["id"]])->update(["status" => 1]);
			if ($results && $articleData["likes_num"] >= 0) {
				$res = \think\facade\Db::name("zh_articles")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "article_id" => $articlesId])->inc("likes_num")->update();
			} else {
				return $this->ajaxReturn($this->successCode, "操作失败");
			}
		} elseif ($zhGiveThumbsUp && $zhGiveThumbsUp["status"] == 1) {
			$results = \think\facade\Db::name("zh_give_thumbs_up")->where(["articles_id" => $articlesId, "u_id" => $uid, "id" => $zhGiveThumbsUp["id"]])->update(["status" => 0]);
			if ($results && $articleData["likes_num"] >= 1) {
				$res = \think\facade\Db::name("zh_articles")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "article_id" => $articlesId])->dec("likes_num")->update();
			} else {
				return $this->ajaxReturn($this->successCode, "操作失败");
			}
		} else {
			if (!$zhGiveThumbsUp) {
				$data = ["wxapp_id" => $wxapp_id, "s_id" => $s_id, "u_id" => $uid, "articles_id" => $articlesId, "status" => 1, "createtime" => time()];
				$results = \think\facade\Db::name("zh_give_thumbs_up")->insert($data);
				if ($results) {
					$res = \think\facade\Db::name("zh_articles")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "article_id" => $articlesId])->inc("likes_num")->update();
				} else {
					return $this->ajaxReturn($this->successCode, "操作失败");
				}
			}
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function forwardAccumulation()
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
		$articlesId = $this->request->post("article_id");
		if (!$articlesId) {
			return $this->ajaxReturn($this->errorCode, "缺少文章参数");
		}
		$articlesData = \think\facade\Db::name("zh_articles")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "article_id" => $articlesId])->find();
		if ($articlesData && $articlesData["collections_num"] >= 0) {
			$res = \think\facade\Db::name("zh_articles")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "article_id" => $articlesId])->inc("collections_num")->update();
		}
		if ($res) {
			return $this->ajaxReturn($this->successCode, "操作成功", $res);
		} else {
			return $this->ajaxReturn($this->successCode, "操作失败");
		}
	}
}