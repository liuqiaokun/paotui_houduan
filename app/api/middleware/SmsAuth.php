<?php

//decode by http://www.yunlu99.com/
namespace app\api\middleware;

class SmsAuth
{
	public function handle($request, \Closure $next)
	{
		$verify_id = $request->param("verify_id", "", "strip_tags,trim");
		$verify = $request->param("verify", "", "strip_tags,trim");
		$mobile = $request->param("mobile", "", "strip_tags,trim");
		if (empty($verify_id) || empty($verify)) {
			return json(["status" => config("my.errorCode"), "msg" => "短信验证ID或者验证码不能为空"]);
		}
		$cacheData = cache($verify_id);
		if ($cacheData["code"] != $verify) {
			return json(["status" => config("my.errorCode"), "msg" => "验证码错误或者已过期"]);
		}
		if ($cacheData["mobile"] != $mobile) {
			return json(["status" => config("my.errorCode"), "msg" => "手机号与验证不一致"]);
		}
		return $next($request);
	}
}