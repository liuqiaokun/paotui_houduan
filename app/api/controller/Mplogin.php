<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Mplogin extends Common
{
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
			if (empty($openid["unionid"])) {
				return view("index", ["text" => "小程序和公众号未关联", "mp_code" => $setting["mp_code"]]);
			}
			$user = \think\facade\Db::name("wechat_user")->where("wxapp_id", $wxapp_id)->where("unionid", $openid["unionid"])->find();
			if ($user) {
				if ($user["mp_openid"] != $openid["openid"]) {
					\think\facade\Db::name("wechat_user")->where("u_id", $user["u_id"])->update(["mp_openid" => $openid["openid"]]);
				}
			}
			if ($openid) {
				return view("index", ["text" => "授权成功，点此返回", "mp_code" => $setting["mp_code"]]);
			} else {
				return view("index", ["text" => "授权失败", "mp_code" => $setting["mp_code"]]);
			}
		}
		$text = "点击授权";
		return view("index", ["text" => "点击授权", "wxapp_id" => $wxapp_id, "appid" => $setting["mp_appid"], "mp_code" => $setting["mp_code"]]);
	}
	public function test()
	{
		return view("test");
	}
	public function tests()
	{
	}
	public function actionSqrcode()
	{
		$wx["timestamp"] = time();
		$wx["appId"] = "wx2a8ea51f67342bad";
		$wx["nonceStr"] = md5(time());
		$wx["jsapi_ticket"] = $this->actionTicket();
		$wx["url"] = "https://" . $_SERVER["HTTP_HOST"] . "/api/Mplogin/test";
		$string = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $wx["jsapi_ticket"], $wx["nonceStr"], $wx["timestamp"], $wx["url"]);
		$wx["signature"] = sha1($string);
		return json(["data" => $wx, "status" => 0]);
	}
	public function actionAccessToken()
	{
		$file = file_get_contents("./token");
		$info = json_decode($file, 1);
		if ($info && $info["time"] > time()) {
			return $info["access_token"];
		}
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx2a8ea51f67342bad&secret=632194c060028f1c5262a23bbe8eef0c";
		$info = file_get_contents($url);
		$info = json_decode($info, 1);
		if ($info) {
			$info["time"] = time() + $info["expires_in"];
			file_put_contents("./token", json_encode($info));
			return $info["access_token"];
		} else {
			return "失败";
		}
	}
	public function actionTicket()
	{
		$file = file_get_contents("./ticket");
		$info = json_decode($file, 1);
		if ($info && $info["time"] > time()) {
			return $info["ticket"];
		}
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $this->actionAccessToken() . "&type=jsapi";
		$info = file_get_contents($url);
		$info = json_decode($info, 1);
		if ($info) {
			$info["time"] = time() + $info["expires_in"];
			file_put_contents("./ticket", json_encode($info));
			return $info["ticket"];
		} else {
			return "失败";
		}
	}
	public function actionCurl($url, $data, $type = "json")
	{
		if ($type == "json") {
			$headers = ["Content-type: application/json;charset=UTF-8", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache"];
			$data = json_encode($data);
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}