<?php

//decode by http://www.yunlu99.com/
namespace app\api\service;

class sendMsgService
{
	public static function sendSms()
	{
	}
	public static function sendTemplateMsg($config, $data)
	{
		file_put_contents("job1.txt", 1111);
		$configs = ["app_id" => $config["appid"], "secret" => $config["appsecret"]];
		$app = \EasyWeChat\Factory::officialAccount($configs);
		$access_token = \app\api\controller\Common::get_token($config);
		$url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=" . $access_token;
		$data["miniprogram_state"] = "formal";
		$result = self::curlPost($url, json_encode($data));
		\think\facade\Db::name("subscribe_log")->insert(["openid" => $data["touser"], "temp_id" => $data["template_id"], "msg" => json_encode($result)]);
		$count_log = \think\facade\Db::name("subscribe_count")->where(["openid" => $data["touser"], "temp_id" => $data["template_id"]])->find();
		if ($count_log && $count_log["count"] >= 1) {
			\think\facade\Db::name("subscribe_count")->where("id", $count_log["id"])->dec("count", 1)->update();
		}
	}
	public static function curlPost($url, $data)
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