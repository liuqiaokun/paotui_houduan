<?php

//腾讯云oss上传

namespace utils\oss;
use think\facade\Log;
use Qcloud\Cos\Client;


class TencentOssService
{
	
	/**
	 * 腾讯云oss上传
	 * @param  array $tempFile 本地图片路径
	 * @return string 图片上传返回的url地址
	 */
	public static function upload($tmpInfo){
		$cosClient = new Client([
			'region' => $tmpInfo['config']['tencent_oss_region'],
			'schema' => 'https',
			'credentials'=>[
				'secretId' => $tmpInfo['config']['tencent_oss_secretid'],
				'secretKey' => $tmpInfo['config']['tencent_oss_secretkey']
			]
		]);

		$data = [
			'Bucket' => $tmpInfo['config']['tencent_oss_bucket'],
			'Key' => \utils\oss\OssService::setKey('03',$tmpInfo),
			'Body' => fopen($tmpInfo['tmp_name'],'rb'),
			'ServerSideEncryption' => 'AES256'
		];
	
		try{
			$result = $cosClient->putObject($data);
			$result = $result->toArray();
			if(isset($result['Key']) && !empty($result['Location'])){
				return 'https://'.$result['Location'];
			}
		}catch(\Exception $e){
			log::error('腾讯云oss错误：'.print_r($e->getMessage(),true));
			throw new \Exception('上传失败');
		}
		
	}
	
}
