<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Base extends Common
{
	public function upload()
	{
		if (!$_FILES) {
			throw new \think\exception\ValidateException("上传验证失败");
		}
		$file = $this->request->file(array_keys($_FILES)[0]);
		$upload_config_id = $this->request->param("upload_config_id", "", "intval");
		$wxapp_id = $this->request->post("wxapp_id");
		$oss = \think\facade\Db::name("upload_oss")->where("wxapp_id", $wxapp_id)->find();
		$suffix = explode(".", $file->getOriginalName())[1];
		if ($suffix == "mp4" || $suffix == "avi" || $suffix == "flv") {
			$max_size = 5242880;
		} else {
			$max_size = 1048576;
		}
		$api_upload_ext = "jpg,png,gif,mp4,doc,docx,pdf,jpeg";
		if (!\think\facade\Validate::fileExt($file, $api_upload_ext)) {
			throw new \think\exception\ValidateException("上传格式有误");
		}
		if (!\think\facade\Validate::fileSize($file, $max_size)) {
			throw new \think\exception\ValidateException("上传文件不可超过1M");
		}
		$upload_hash_status = !is_null(config("my.upload_hash_status")) ? config("my.upload_hash_status") : true;
		$fileinfo = $upload_hash_status ? db("file")->where("hash", $file->hash("md5"))->find() : false;
		if ($upload_hash_status && $fileinfo && $this->checkFileExists($fileinfo["filepath"])) {
			$url = $fileinfo["filepath"];
		} else {
			$url = $this->up($file, $upload_config_id, $oss);
		}
		if (in_array($suffix, ["jpg", "png", "jpeg"])) {
			if ($judge = $this->img_check($url, $wxapp_id)) {
				return json(["status" => config("my.successCode"), "data" => $url]);
			} else {
				return json(["status" => config("my.errorCode"), "data" => 0, "msg" => "图片存在潜在风险"]);
			}
		} else {
			return json(["status" => config("my.successCode"), "data" => $url]);
		}
	}
	protected function up($file, $upload_config_id, $oss)
	{
		try {
			if ($oss["oss_status"]) {
				$url = \utils\oss\OssService::OssUpload(["tmp_name" => $file->getPathname(), "extension" => $file->extension(), "config" => $oss]);
			} else {
				$info = \think\facade\Filesystem::disk("public")->putFile(\utils\oss\OssService::setFilepath(), $file, "uniqid");
				$url = \utils\oss\OssService::getApiFileName(basename($info));
				if ($upload_config_id && !config("my.oss_status") && in_array(pathinfo($info)["extension"], ["jpg", "png", "gif", "jpeg", "bmp"])) {
					$this->thumb(config("my.upload_dir") . "/" . $info, $upload_config_id);
				}
			}
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		$upload_hash_status = !is_null(config("my.upload_hash_status")) ? config("my.upload_hash_status") : true;
		$upload_hash_status && db("file")->insert(["filepath" => $url, "hash" => $file->hash("md5"), "create_time" => time()]);
		return $url;
	}
	private function thumb($imagesUrl, $upload_config_id)
	{
		$configInfo = db("upload_config")->where("id", $upload_config_id)->find();
		if ($configInfo) {
			$image = \think\Image::open($imagesUrl);
			$targetimages = $imagesUrl;
			if (!$configInfo["upload_replace"]) {
				$fileinfo = pathinfo($imagesUrl);
				$targetimages = $fileinfo["dirname"] . "/s_" . $fileinfo["basename"];
				copy($imagesUrl, $targetimages);
			}
			if ($configInfo["thumb_status"]) {
				$image->thumb($configInfo["thumb_width"], $configInfo["thumb_height"], $configInfo["thumb_type"])->save($targetimages);
			}
			$config = db("config")->column("data", "name");
			if (file_exists("." . $config["water_logo"]) && $config["water_status"] && $config["water_position"]) {
				$image->water("." . $config["water_logo"], $config["water_position"])->save($targetimages);
			}
		}
	}
	public function checkFileExists($filepath)
	{
		if (strpos($filepath, "://")) {
			$res = file_get_contents($filepath) ? true : false;
		} else {
			$res = file_exists("." . $filepath) ? true : false;
		}
		return $res;
	}
	public function captcha()
	{
		ob_clean();
		return captcha();
	}
	public function img_check($img, $wxapp_id)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$img = file_get_contents($img);
		$filePath = "tmp" . rand(1, 100) . ".png";
		file_put_contents($filePath, $img);
		$obj = new \CURLFile(realpath($filePath));
		$obj->setMimeType("image/jpeg");
		$file["media"] = $obj;
		$configs = ["app_id" => $config["appid"], "secret" => $config["appsecret"]];
		$app = \EasyWeChat\Factory::officialAccount($configs);
		$token = $this->get_token($config);
		$url1 = "https://api.weixin.qq.com/wxa/img_sec_check?access_token={$token}";
		$info = $this->http_request($url1, $file);
		$result = json_decode($info, true);
		unlink($filePath);
		if ($result["errcode"] != 0) {
			return false;
		} else {
			return true;
		}
	}
	public function test_token($token)
	{
		$url1 = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token={$token}";
		$info = $this->http_request($url1, "法轮功");
		$result = json_decode($info, true);
		return $result["errcode"];
	}
	public function msg_check($content, $wxapp_id)
	{
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$configs = ["app_id" => $config["appid"], "secret" => $config["appsecret"]];
		$app = \EasyWeChat\Factory::officialAccount($configs);
		$token = $app->access_token->getToken()["access_token"];
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
			return $this->result(1, "内容含有违法违规内容", 0);
		}
	}
	public function http_request($url, $data = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($curl);
		curl_close($curl);
		file_put_contents("/tmp/heka_weixin." . date("Ymd") . ".log", date("Y-m-d H:i:s") . "\t" . $output . "\n", FILE_APPEND);
		return $output;
	}
}