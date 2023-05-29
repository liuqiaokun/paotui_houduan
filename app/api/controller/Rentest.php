<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Rentest extends Common
{
	public function phone()
	{
		$code = $this->request->post("code");
		$encryptedData = $this->request->post("encryptedData");
		$iv = $this->request->post("iv");
		$config = ["app_id" => "wxb40cc4d3fb44c393", "secret" => "915d3946e75da1800f16737b1ae67ad7"];
		$app = \EasyWeChat\Factory::miniProgram($config);
		$info = $app->auth->session($code);
		$decryptedData = $app->encryptor->decryptData($info["session_key"], $iv, $encryptedData);
		print_r($decryptedData);
		print_r($decryptedData["phoneNumber"]);
	}
	public function step()
	{
		$code = $this->request->post("code");
		$encryptedData = $this->request->post("encryptedData");
		$iv = $this->request->post("iv");
		$config = ["app_id" => "wxb40cc4d3fb44c393", "secret" => "915d3946e75da1800f16737b1ae67ad7"];
		$app = \EasyWeChat\Factory::miniProgram($config);
		$info = $app->auth->session($code);
		$decryptedData = $app->encryptor->decryptData($info["session_key"], $iv, $encryptedData);
		print_r($decryptedData);
	}
	public function index()
	{
		$code = $this->request->get("code");
		$wxapp_id = $this->request->get("wxapp_id");
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if ($code) {
			$config = ["app_id" => $setting["mp_appid"], "secret" => $setting["mp_secret"], "response_type" => "array"];
			$app = \EasyWeChat\Factory::officialAccount($config);
			$token = $app->oauth->getAccessToken($code);
			$openid = $app->user->get($token["openid"]);
			$user = \think\facade\Db::name("wechat_user")->where("wxapp_id", $wxapp_id)->where("unionid", $openid["unionid"])->find();
			if ($user) {
				if ($user["mp_openid"] != $openid["openid"]) {
					\think\facade\Db::name("wechat_user")->where("u_id", $user["u_id"])->update(["mp_openid" => $openid["openid"]]);
				}
			}
			if ($openid) {
				return view("index", ["text" => "授权成功"]);
			} else {
				return view("index", ["text" => "授权失败"]);
			}
		}
		$text = "点击授权";
		return view("index", ["text" => "点击授权", "wxapp_id" => $wxapp_id, "appid" => $setting["mp_appid"]]);
	}
}