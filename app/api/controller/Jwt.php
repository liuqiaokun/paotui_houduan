<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Jwt
{
	private static $instance;
	private $token;
	private $decodeToken;
	private $iss;
	private $aud;
	private $uid;
	private $secrect;
	private $expTime;
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	private function __construct()
	{
	}
	public function getToken()
	{
		return \strval($this->token);
	}
	public function setToken($token)
	{
		$this->token = $token;
		return $this;
	}
	public function setUid($uid)
	{
		$this->uid = $uid;
		return $this;
	}
	public function getUid()
	{
		return $this->uid;
	}
	public function setExpTime($expTime)
	{
		$this->expTime = $expTime;
		return $this;
	}
	public function setIss($iss)
	{
		$this->iss = $iss;
		return $this;
	}
	public function setAud($aud)
	{
		$this->aud = $aud;
		return $this;
	}
	public function setSecrect($secrect)
	{
		$this->secrect = $secrect;
		return $this;
	}
	public function encode()
	{
		$time = time();
		$this->token = (new \Lcobucci\JWT\Builder())->setHeader("alg", "HS256")->setIssuer($this->iss)->setAudience($this->aud)->setIssuedAt($time)->setExpiration($time + $this->expTime)->set("uid", $this->uid)->sign(new \Lcobucci\JWT\Signer\Hmac\Sha256(), $this->secrect)->getToken();
		return $this;
	}
	public function decode()
	{
		try {
			$this->decodeToken = (new \Lcobucci\JWT\Parser())->parse(\strval($this->token));
			$this->uid = $this->decodeToken->getClaim("uid");
			return $this->decodeToken;
		} catch (RuntimeException $e) {
			throw new \Exception($e->getMessage());
		}
	}
	public function validate()
	{
		$data = new \Lcobucci\JWT\ValidationData();
		$data->setIssuer($this->iss);
		$data->setAudience($this->aud);
		$data->setId($this->uid);
		return $this->decode()->validate($data);
	}
	public function verify()
	{
		return $this->decode()->verify(new \Lcobucci\JWT\Signer\Hmac\Sha256(), $this->secrect);
	}
}