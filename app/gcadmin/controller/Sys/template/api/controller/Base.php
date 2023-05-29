<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

class Base extends Common
{
	public function upload()
	{
		if (!$_FILES) {
			throw new \think\exception\ValidateException("上传验证失败");
		}
		$file = $this->request->file(array_keys($_FILES)[0]);
		$upload_config_id = $this->request->param("upload_config_id", "", "intval");
		if (!\think\facade\Validate::fileExt($file, config("my.api_upload_ext")) || !\think\facade\Validate::fileSize($file, config("my.api_upload_max"))) {
			throw new \think\exception\ValidateException("上传验证失败");
		}
		$upload_hash_status = !is_null(config("my.upload_hash_status")) ? config("my.upload_hash_status") : true;
		$fileinfo = $upload_hash_status ? db("file")->where("hash", $file->hash("md5"))->find() : false;
		if ($upload_hash_status && $fileinfo && $this->checkFileExists($fileinfo["filepath"])) {
			$url = $fileinfo["filepath"];
		} else {
			$url = $this->up($file, $upload_config_id);
		}
		return json(["status" => config("my.successCode"), "data" => $url]);
	}
	protected function up($file, $upload_config_id)
	{
		try {
			if (config("my.oss_status")) {
				$url = \utils\oss\OssService::OssUpload(["tmp_name" => $file->getPathname(), "extension" => $file->extension()]);
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
}