<?php

//decode by http://www.yunlu99.com/
namespace app\api\middleware;

class JwtAuth
{
	public function handle($request, \Closure $next)
	{
		$token = $request->header("Authorization");
		if (!$token) {
			return json(["status" => config("my.errorCode"), "msg" => "请先授权登录"]);
		}
		if (count(explode(".", $token)) != 3) {
			return json(["status" => config("my.errorCode"), "msg" => "请先授权登录"]);
		}
		$jwt = \app\api\controller\Jwt::getInstance();
		$jwt->setIss(config("my.jwt_iss"))->setAud(config("my.jwt_aud"))->setSecrect(config("my.jwt_secrect"))->setToken($token);
		if ($jwt->decode()->getClaim("exp") < time()) {
			return json(["status" => config("my.jwtExpireCode"), "msg" => "token过期"]);
		}
		if ($jwt->validate() && $jwt->verify()) {
			$request->uid = $jwt->decode()->getClaim("uid");
			$user = \app\api\model\WechatUser::where("u_id", $request->uid)->find();
			if ($user["is_black"] == 1) {
				return json(["status" => config("my.errorCode"), "msg" => "您的账号已被禁用，请联系客服。"]);
			}
			return $next($request);
		} else {
			return json(["status" => config("my.jwtErrorCode"), "msg" => "token失效"]);
		}
	}
}