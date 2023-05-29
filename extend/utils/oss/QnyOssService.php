<?php

//七牛云oss上传

namespace utils\oss;
use think\facade\Log;

class QnyOssService
{
	
	/**
	 * 阿里云oss上传
	 * @param  array $tempFile 本地图片路径
	 * @return string 图片上传返回的url地址
	 */
	public static function upload($tmpInfo){
//	    print_r($tmpInfo['config']);die;
		$auth = new \Qiniu\Auth($tmpInfo['config']['qny_oss_accesskey'], $tmpInfo['config']['qny_oss_secretkey']);
		$upToken = $auth->uploadToken($tmpInfo['config']['qny_oss_bucket']);
		$uploadMgr = new \Qiniu\Storage\UploadManager();
		try{
			$ret = $uploadMgr->putFile($upToken, \utils\oss\OssService::setKey('02',$tmpInfo),$tmpInfo['tmp_name']);
		}catch(\Exception $e){
			log::error('七牛云oss错误：'.print_r($e->getMessage(),true));
			throw new \Exception('上传失败');
		}
		return $tmpInfo['config']['qny_oss_domain'].$ret[0]['key'];
	}
    
}
