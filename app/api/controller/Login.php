<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Login extends Common
{
	public function miniLogin()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$spid = $this->request->post("spid");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$setting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		try {
			$config = ["app_id" => $setting["appid"], "secret" => $setting["appsecret"], "response_type" => "array"];
			$app = \EasyWeChat\Factory::miniProgram($config);
			$wxuser = $app->auth->session($this->request->param("code"));
		} catch (\Exception $e) {
			throw new \think\exception\ValidateException($e->getMessage());
		}
		$openid = $wxuser["openid"];
		if ($openid) {
			$nickname = $this->request->param("nickName");
			$user = \think\facade\Db::name("wechat_user")->where("openid", $openid)->where("wxapp_id", $wxapp_id)->find();
			if (empty($user)) {
				$insert["nickname"] = $nickname;
				$insert["avatar"] = $this->request->param("avatar");
				$insert["openid"] = $openid;
				$insert["unionid"] = $wxuser["unionid"] ? $wxuser["unionid"] : "";
				$insert["wxapp_id"] = $wxapp_id;
				$insert["balance"] = 0;
				$insert["run_status"] = 0;
				$insert["create_time"] = time();
				$insert["store_balance"] = 0;
				if ($spid) {
					$insert["spid"] = $spid;
					$insert["spid_time"] = date("Y-m-d H:i:s");
				}
				if (!empty($wxuser["unionid"])) {
					$open = $this->app->db->name("wechat_user")->where("unionid", $wxuser["unionid"])->find();
					if ($open) {
						$opendata = ["openid" => $openid, "wxapp_id" => $wxapp_id];
						$open = $this->app->db->name("wechat_user")->where("unionid", $insert["unionid"])->update($opendata);
						$id = $open["u_id"];
					} else {
						$id = \think\facade\Db::name("wechat_user")->insertGetId($insert);
					}
				} else {
					$id = \think\facade\Db::name("wechat_user")->insertGetId($insert);
				}
			} else {
				if ($spid) {
					$par = \think\facade\Db::name("wechat_user")->find($spid);
					if ($par["spid"] != $user["u_id"]) {
						$update = ["unionid" => $wxuser["unionid"], "spid" => $spid, "spid_time" => date("Y-m-d H:i:s")];
					} else {
						$update = ["unionid" => $wxuser["unionid"]];
					}
				} else {
					$update = ["unionid" => $wxuser["unionid"]];
				}
				\think\facade\Db::name("wechat_user")->where("u_id", $user["u_id"])->update($update);
				$id = $user["u_id"];
			}
			return $this->ajaxReturn($this->successCode, "获取openid成功", "", $this->setToken($id));
		} else {
			return $this->ajaxReturn($this->errorCode, "获取openid失败", $wxuser["errmsg"]);
		}
	}
	public function loginCheck()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$setting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		try {
			$config = ["app_id" => $setting["appid"], "secret" => $setting["appsecret"], "response_type" => "array"];
			$app = \EasyWeChat\Factory::miniProgram($config);
			$wxuser = $app->auth->session($this->request->param("code"));
		} catch (\Exception $e) {
			throw new \think\exception\ValidateException($e->getMessage());
		}
		$openid = $wxuser["openid"];
		$user = \think\facade\Db::name("wechat_user")->where("openid", $openid)->where("wxapp_id", $wxapp_id)->find();
		if ($user) {
			$user["token"] = $this->setToken($user["u_id"]);
		}
		return $this->ajaxReturn($this->successCode, "获取成功", "", $user);
	}
}