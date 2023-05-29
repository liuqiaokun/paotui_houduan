<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

class Upload extends Admin
{
	private function upload($filekey)
	{
		$file = $this->request->file($filekey);
		$upload_config_id = $this->request->get("upload_config_id", "", "intval");
		$file_type = upload_replace(config("xhadmin.file_type"));
		if (!\think\facade\Validate::fileExt($file, $file_type) || !\think\facade\Validate::fileSize($file, config("xhadmin.file_size") * 1024 * 1024)) {
			throw new \think\exception\ValidateException("上传验证失败");
		}
		$upload_hash_status = !is_null(config("my.upload_hash_status")) ? config("my.upload_hash_status") : true;
		$fileinfo = $upload_hash_status ? db("file")->where("hash", $file->hash("md5"))->find() : false;
		if ($upload_hash_status && $fileinfo && $this->checkFileExists($fileinfo["filepath"])) {
			$url = $fileinfo["filepath"];
		} else {
			$url = $this->up($file, $upload_config_id);
		}
		return $url;
	}
	protected function up($file, $upload_config_id)
	{
		try {
			if (config("my.oss_status")) {
				$url = \utils\oss\OssService::OssUpload(["tmp_name" => $file->getPathname(), "extension" => $file->extension()]);
			} else {
				$info = \think\facade\Filesystem::disk("public")->putFile(\utils\oss\OssService::setFilepath(), $file, "uniqid");
				$url = \utils\oss\OssService::getAdminFileName(basename($info));
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
	public function uploadImages()
	{
		$url = $this->upload("file");
		if ($url) {
			return json(["code" => 1, "data" => $url]);
		} else {
			return json(["code" => 0, "msg" => "上传失败"]);
		}
	}
	public function editorUpload()
	{
		$url = $this->upload("filedata");
		if ($url) {
			echo "{err: \"\", msg: {url: \"!" . $url . "\", localname: \"\", id: \"1\"}}";
		}
	}
	public function uploadUeditor()
	{
		$ueditor_config = json_decode(preg_replace("/\\/\\*[\\s\\S]+?\\*\\//", "", file_get_contents("static/js/ueditor/php/config.json")), true);
		$action = $_GET["action"];
		switch ($action) {
			case "config":
				$result = json_encode($ueditor_config);
				break;
			case "uploadimage":
			case "uploadscrawl":
			case "uploadvideo":
			case "uploadfile":
				$url = $this->upload("upfile");
				$result = json_encode(["url" => $url, "title" => htmlspecialchars($_POST["pictitle"], ENT_QUOTES), "original" => basename($url), "state" => "SUCCESS"]);
				break;
			default:
				$result = json_encode(["state" => "请求地址出错"]);
				break;
		}
		if (isset($_GET["callback"])) {
			if (preg_match("/^[\\w_]+\$/", $_GET["callback"])) {
				echo htmlspecialchars($_GET["callback"]) . "(" . $result . ")";
			} else {
				echo json_encode(["state" => "callback参数不合法"]);
			}
		} else {
			echo $result;
		}
	}
	public function markDownUpload()
	{
		$url = $this->upload("editormd-image-file");
		if ($url) {
			return json(["url" => $url, "success" => 1, "message" => "图片上传成功!"]);
		}
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
			if (file_exists("." . config("xhadmin.water_logo")) && config("xhadmin.water_status") && config("xhadmin.water_position")) {
				$image->water("." . config("xhadmin.water_logo"), config("xhadmin.water_position"))->save($targetimages);
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
}