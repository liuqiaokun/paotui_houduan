<?php

//decode by http://www.yunlu99.com/
namespace app\api\middleware;

class CaptchaAuth
{
	public function handle($request, \Closure $next)
	{
		$captcha = $request->param("captcha", "", "strip_tags,trim");
		if (empty($captcha)) {
			return json(["status" => config("my.errorCode"), "msg" => "验证码不能为空"]);
		}
		if (!captcha_check($captcha)) {
			return json(["status" => config("my.errorCode"), "msg" => "验证码错误"]);
		}
		return $next($request);
	}
}