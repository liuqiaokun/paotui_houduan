<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhArticles extends Common
{
	public function index()
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
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$type = $this->request->get("type");
		$where = [];
		$class_id = $this->request->get("class_id", "", "serach_in");
		if ($class_id) {
			$where["class_id"] = $this->request->get("class_id", "", "serach_in");
		}
		$where["status"] = 1;
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$where["delete_time"] = null;
		$list = \think\facade\Db::name("zh_articles")->where("status", 1)->where("is_expired", 1)->select();
		foreach ($list as &$v) {
			if ($v["deadtime"] < time()) {
				\think\facade\Db::name("zh_articles")->where("article_id", $v["article_id"])->update(["is_expired" => 0, "paytime" => null]);
			}
		}
		$token = \think\facade\Request::header("Authorization");
		if ($token) {
			$jwt = Jwt::getInstance();
			$jwt->setIss(config("my.jwt_iss"))->setAud(config("my.jwt_aud"))->setSecrect(config("my.jwt_secrect"))->setToken($token);
			$uid = $jwt->decode()->getClaim("uid");
		}
		$topList = \think\facade\Db::name("zh_articles")->where("is_expired", 1)->where($where)->count();
		if ($type == 1) {
			if ($topList > 0) {
				$field = "*,comments_num + likes_num s";
				$orderby = "is_expired desc,paytime asc,s desc";
			} else {
				$field = "*,comments_num + likes_num s";
				$orderby = "s desc";
			}
		} else {
			if ($topList > 0) {
				$field = "*";
				$orderby = "is_expired desc,paytime asc,article_id desc";
			} else {
				$field = "*";
				$orderby = "article_id desc";
			}
		}
		$res = \app\api\service\ZhArticlesService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		$class = \think\facade\Db::name("zh_forum_class")->where("wxapp_id", $wxapp_id)->where("s_id", $s_id)->select();
		$class_list = [];
		foreach ($class as &$v) {
			$class_list[$v["class_id"]] = ["class_name" => $v["class_name"], "is_auth" => $v["is_auth_watch"]];
		}
		foreach ($res["list"] as &$v) {
			if ($uid) {
				$is_fav = \think\facade\Db::name("zh_give_thumbs_up")->where("articles_id", $v["article_id"])->where("u_id", $uid)->where("status", 1)->find();
				$v["is_fav"] = $is_fav ? 1 : 0;
			} else {
				$v["is_fav"] = 0;
			}
			$wechatUserData = \think\facade\Db::name("wechat_user")->where("u_id", $v["u_id"])->find();
			$v["nickname"] = $wechatUserData["nickname"];
			$v["avatar"] = $wechatUserData["avatar"];
			if ($v["media_type"] == 2 && $v["image"]) {
				$v["image"] = explode(",", $v["image"]);
			}
			$v["createtime"] = date("Y-m-d H:i", $v["createtime"]);
			$v["class_name"] = $class_list[$v["class_id"]]["class_name"];
			$v["is_auth"] = $class_list[$v["class_id"]]["is_auth"];
			$exp = explode(",", $v["topic"]);
			$topic = [];
			foreach ($exp as $ke => $ve) {
				$topic[$ke] = $this->app->db->name("dmh_topic")->where("id", $ve)->find();
			}
			$v["topic"] = $topic;
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$price = $this->request->post("price");
		$day = $this->request->post("day");
		$is_top = $this->request->post("is_top");
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
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$postField = "class_id,content,image,video,is_anonymous,topic";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["s_id"] = $s_id;
		$data["wxapp_id"] = $wxapp_id;
		$data["u_id"] = $uid;
		$data["createtime"] = time();
		$data["is_top"] = $is_top;
		$settingData = \think\facade\Db::name("setting")->where(["wxapp_id" => $wxapp_id])->value("article_check_switch");
		if ($settingData) {
			$data["status"] = 0;
		} else {
			$data["status"] = 1;
		}
		if ($data["image"] && !$data["video"]) {
			$data["media_type"] = 2;
		} else {
			if (!$data["image"] && $data["video"]) {
				$data["media_type"] = 1;
			}
		}
		$judge = $this->msg_check($data["content"], $data["wxapp_id"]);
		if (!$judge) {
			return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
		}
		$res = \app\api\model\ZhArticles::insertGetId($data);
		if ($is_top && $price > 0) {
			$result = ZhArticlesPay::topPay($res, $wxapp_id, $s_id, $price, $day, 1, $uid);
			return $this->ajaxReturn($this->successCode, "返回成功", $result);
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function reward()
	{
		$a_id = $this->request->post("a_id");
		$wxapp_id = $this->request->post("wxapp_id");
		$price = $this->request->post("price");
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
		$result = ZhArticlesPay::topPay($a_id, $wxapp_id, $s_id, $price, 0, 2, $uid);
		return $this->ajaxReturn($this->successCode, "返回成功", $result);
	}
	public function view()
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
		$data["article_id"] = $this->request->get("article_id", "", "serach_in");
		$data["wxapp_id"] = $wxapp_id;
		$field = "*";
		$res = checkData(\app\api\model\ZhArticles::field($field)->where($data)->find());
		\think\facade\Db::name("zh_articles")->where("article_id", $data["article_id"])->inc("views_num")->update();
		$userData = \think\facade\Db::name("wechat_user")->where("u_id", $res["u_id"])->find();
		$uid = 0;
		$token = \think\facade\Request::header("Authorization");
		if ($token) {
			$jwt = Jwt::getInstance();
			$jwt->setIss(config("my.jwt_iss"))->setAud(config("my.jwt_aud"))->setSecrect(config("my.jwt_secrect"))->setToken($token);
			$uid = $jwt->decode()->getClaim("uid");
		}
		if ($uid) {
			$zhGiveThumbsUp = \think\facade\Db::name("zh_give_thumbs_up")->where(["articles_id" => $data["article_id"], "u_id" => $uid])->find();
			if ($zhGiveThumbsUp && $zhGiveThumbsUp["status"] == 0) {
				$res["is_fav"] = false;
			} elseif ($zhGiveThumbsUp && $zhGiveThumbsUp["status"] == 1) {
				$res["is_fav"] = true;
			} else {
				if (!$zhGiveThumbsUp) {
					$res["is_fav"] = false;
				}
			}
		} else {
			$res["is_fav"] = false;
		}
		$res["nickname"] = $userData["nickname"];
		$res["avatar"] = $userData["avatar"];
		$res["u_id"] = $userData["u_id"];
		$res["createtime"] = date("Y-m-d H:i:s", $res["createtime"]);
		if ($res["media_type"] == 2 && $res["image"]) {
			$res["image"] = explode(",", $res["image"]);
		}
		$commentListData = \think\facade\Db::name("zh_commenes")->where(["article_id" => $res["article_id"], "wxapp_id" => $wxapp_id, "p_id" => 0])->select()->toArray();
		foreach ($commentListData as &$value) {
			$wxData = \think\facade\Db::name("wechat_user")->where("u_id", $value["u_id"])->find();
			$value["nickname"] = $wxData["nickname"];
			$value["avatar"] = $wxData["avatar"];
			$a = $this->commentRecursion($value["id"], $value["u_id"], "gc_zh_commenes");
			$value["child"] = $a;
			$value["block"] = false;
			$time = new \utils\Time();
			$value["createtime"] = $time->timeDiff($value["createtime"]);
			$value["zan_num"] = \think\facade\Db::name("zh_forum_comment_fav")->where("c_id", $value["id"])->where("status", 1)->count();
			if ($uid) {
				$record = \think\facade\Db::name("zh_forum_comment_fav")->where(["c_id" => $value["id"], "uid" => $uid, "status" => 1])->find();
				$value["is_zan"] = $record ? 1 : 0;
			} else {
				$value["is_zan"] = 0;
			}
		}
		unset($value);
		$follow_data = \think\facade\Db::name("user_follow")->where("f_uid", $res["u_id"])->where("wxapp_id", $wxapp_id)->where("uid", $uid)->where("status", 1)->find();
		$res["is_follow"] = $follow_data ? 1 : 0;
		$res["is_my"] = $res["u_id"] == $uid ? 1 : 0;
		$res["comment"] = $commentListData;
		$res["is_open_reward"] = $wxSetting["is_open_reward"];
		$sSetting = \think\facade\Db::name("school")->where("s_id", $res["s_id"])->find();
		$res["reward_list"] = explode(";", $sSetting["reward"]);
		$where["p.wxapp_id"] = $wxapp_id;
		$where["p.a_id"] = $res["article_id"];
		$where["p.type"] = 2;
		$where["p.status"] = 1;
		$where["p.type"] = 2;
		$group = $type == 1 ? "" : "p.uid";
		$res["reward_user_list"] = \think\facade\Db::name("article_pay")->alias("p")->group($group)->join("wechat_user u", "p.uid = u.u_id")->where($where)->field("p.*,u.avatar,u.nickname")->order("id", "desc")->select();
		$res["class_name"] = \think\facade\Db::name("zh_forum_class")->where("class_id", $res["class_id"])->value("class_name");
		$res["user"] = [];
		if ($uid) {
			$res["user"] = \think\facade\Db::name("wechat_user")->find($uid);
		}
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function getMyArticle()
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
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$status = $this->request->get("status");
		$where = [];
		$where["u_id"] = $uid;
		if ($status == 0) {
			$where["status"] = 0;
		}
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$where["delete_time"] = ["exp", "is null"];
		$field = "class_id,u_id,content,status,createtime,image,video,is_anonymous,media_type,comments_num,likes_num,collections_num,article_id";
		$orderby = "article_id desc";
		$class = \think\facade\Db::name("zh_forum_class")->where("wxapp_id", $wxapp_id)->where("s_id", $s_id)->select();
		$class_list = [];
		foreach ($class as &$v) {
			$class_list[$v["class_id"]] = $v["class_name"];
		}
		$res = \app\api\service\ZhArticlesService::getMyArticleList(formatWhere($where), $field, $orderby, $limit, $page);
		$wechatUserData = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		foreach ($res["list"] as &$v) {
			$is_fav = \think\facade\Db::name("zh_give_thumbs_up")->where("articles_id", $v["article_id"])->where("u_id", $uid)->where("status", 1)->find();
			$v["nickname"] = $wechatUserData["nickname"];
			$v["avatar"] = $wechatUserData["avatar"];
			if ($v["media_type"] == 2 && $v["image"]) {
				$v["image"] = explode(",", $v["image"]);
			}
			$v["createtime"] = date("Y-m-d H:i", $v["createtime"]);
			$v["class_name"] = $class_list[$v["class_id"]];
			$v["is_fav"] = $is_fav ? 1 : 0;
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
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$idx = $this->request->post("article_id", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["article_id"] = $idx;
		$data["wxapp_id"] = $wxapp_id;
		$data["s_id"] = $s_id;
		$data["u_id"] = $uid;
		try {
			$r = \app\api\model\ZhArticles::destroy($data, false);
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
			$v["contents"] = "回复@" . $p_user["nickname"] . ":" . $v["contents"];
			$result[] = $v;
			$result = array_merge($result, $this->commentRecursion($v["id"], $v["u_id"], $table));
		}
		return $result;
	}
	public function getMyFavArticle()
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
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$where = [];
		$where["u_id"] = $uid;
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$field = "class_id,u_id,content,status,createtime,image,video,is_anonymous,media_type,comments_num,likes_num,collections_num,article_id";
		$orderby = "article_id desc";
		$class = \think\facade\Db::name("zh_forum_class")->where("wxapp_id", $wxapp_id)->where("s_id", $s_id)->select();
		$class_list = [];
		foreach ($class as &$v) {
			$class_list[$v["class_id"]] = $v["class_name"];
		}
		$res = \think\facade\Db::name("zh_give_thumbs_up")->alias("g")->join("zh_articles a", "a.article_id = g.articles_id")->join("wechat_user u", "a.u_id = u.u_id")->field("g.id,a.*,u.avatar,u.nickname")->where("g.s_id", $s_id)->where("g.u_id", $uid)->where("g.status", 1)->page($page, $limit)->select()->toArray();
		foreach ($res as &$v) {
			$is_fav = \think\facade\Db::name("zh_give_thumbs_up")->where("articles_id", $v["article_id"])->where("u_id", $uid)->where("status", 1)->find();
			if ($v["media_type"] == 2 && $v["image"]) {
				$v["image"] = explode(",", $v["image"]);
			}
			$v["createtime"] = date("Y-m-d H:i", $v["createtime"]);
			$v["class_name"] = $class_list[$v["class_id"]];
			$v["is_fav"] = $is_fav ? 1 : 0;
		}
		unset($v);
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function robot()
	{
		$articles = $this->app->db->name("zh_articles")->where("status", 1)->where("returntype", 0)->order("article_id desc")->select();
		$datas = [];
		foreach ($articles as $key => $v) {
			$res = $this->app->db->name("zh_articles")->where("article_id", $v["article_id"])->update(["returntype" => 1]);
			$url = $this->urls($v["wxapp_id"], $v);
			$datas[$key]["s_id"] = $v["s_id"];
			$content = mb_substr($v["content"], 0, 10);
			$datas[$key]["content"] = $content . "...";
			$datas[$key]["url"] = $url;
		}
		if ($datas) {
			return $this->ajaxReturn($this->successCode, "返回成功", $datas);
		} else {
			return $this->ajaxReturn($this->errorCode, "未查询到最新参数", []);
		}
	}
	public function urls($wxapp_id, $data)
	{
		$peiz = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$token = $this->token($peiz);
		$url = "https://api.weixin.qq.com/wxa/genwxashortlink?access_token=" . $token;
		$articleid = "id=" . $data["article_id"];
		$data = json_encode(["page_url" => "gc_school/pages/article/detail?" . $articleid, "is_permanent" => true]);
		$url = json_decode($this->curlPost($url, $data), true);
		return $url["link"];
	}
	public function token($wxapp)
	{
		$url = "https://api.weixin.qq.com/cgi-bin/token";
		$data = ["grant_type" => "client_credential", "appid" => $wxapp["appid"], "secret" => $wxapp["appsecret"]];
		$token = json_decode($this->curlPost($url, $data), true);
		return $token["access_token"];
	}
	protected function curlPost($url, $data)
	{
		$ch = curl_init();
		$params[CURLOPT_URL] = $url;
		$params[CURLOPT_HEADER] = false;
		$params[CURLOPT_SSL_VERIFYPEER] = false;
		$params[CURLOPT_SSL_VERIFYHOST] = false;
		$params[CURLOPT_RETURNTRANSFER] = true;
		$params[CURLOPT_POST] = true;
		$params[CURLOPT_POSTFIELDS] = $data;
		curl_setopt_array($ch, $params);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
}