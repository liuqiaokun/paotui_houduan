<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

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
}