<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Common
{
	protected $request;
	protected $app;
	protected $_data;
	protected $successCode;
	protected $errorCode;
	public function __construct(\think\App $app)
	{
		$this->app = $app;
		$this->request = $this->app->request;
		$this->_data = $this->request->param();
		if (!$this->request->isJson()) {
			$this->_data = $this->request->param();
		} else {
			$this->_data = json_decode(file_get_contents("php://input"), true);
		}
		$this->_data["timestamp"] = date("Y-m-d H:i:s", time());
		$this->successCode = config("my.successCode");
		$this->errorCode = config("my.errorCode");
		if (config("my.api_input_log")) {
			\think\facade\Log::info("接口地址：" . request()->pathinfo() . ",接口输入：" . print_r($this->_data, true));
		}
	}
	protected function setToken($uid)
	{
		$jwt = Jwt::getInstance();
		$jwt->setIss(config("my.jwt_iss"))->setAud(config("my.jwt_aud"))->setSecrect(config("my.jwt_secrect"))->setExpTime(config("my.jwt_expire_time"));
		$token = $jwt->setUid($uid)->encode()->getToken();
		return $token;
	}
	protected function ajaxReturn($status, $msg, $data = "", $token = "")
	{
		$res = ["status" => $status, "msg" => $msg];
		!empty($data) && ($res["data"] = $data);
		!empty($token) && ($res["token"] = $token);
		return json($res);
	}
	public function __call($method, $args)
	{
		throw new \think\exception\FuncNotFoundException("方法不存在", $method);
	}
	public function msg_check($content, $wxapp_id)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$token = $this->get_token($config);
		$url = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token=" . $token;
		$datas = json_encode(["content" => $content], JSON_UNESCAPED_UNICODE);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
		$res = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($res, true);
		if ($result["errcode"] != 0) {
			return false;
		} else {
			return true;
		}
	}
	public function getDistance($lat1, $lng1, $lat2, $lng2)
	{
		$radLat1 = deg2rad(floatval($lat1));
		$radLat2 = deg2rad(floatval($lat2));
		$radLng1 = deg2rad(floatval($lng1));
		$radLng2 = deg2rad(floatval($lng2));
		$a = $radLat1 - $radLat2;
		$b = $radLng1 - $radLng2;
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
		if ($s * 1000 < 1000) {
			return round($s * 1000, 2) . "m";
		} else {
			return round($s, 2) . "km";
		}
	}
	public static function get_token($set)
	{
		$config = ["app_id" => $set["appid"], "secret" => $set["appsecret"]];
		$app = \EasyWeChat\Factory::miniProgram($config);
		$token = $app->access_token->getToken()["access_token"];
		$content = "法轮功";
		$url = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token=" . $token;
		$datas = json_encode(["content" => $content], JSON_UNESCAPED_UNICODE);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
		$res = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($res, true);
		file_put_contents("access.txt", $token . " 生成access_token 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		if ($result["errcode"] == 40001) {
			file_put_contents("access.txt", " 强刷token，生成access_token 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
			$token = $app->access_token->getToken(true)["access_token"];
		}
		return $token;
	}
}