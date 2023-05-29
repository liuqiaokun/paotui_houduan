<?php

//decode by http://www.yunlu99.com/
declare (strict_types=1);
namespace app;

abstract class RenController
{
	const VERSION = "4.201015";
	protected $request;
	protected $app;
	protected $batchValidate = false;
	protected $middleware = [];
	public function __construct(\think\App $app, \think\View $view)
	{
		$this->app = $app;
		$this->request = $this->app->request;
		$this->view = $view;
		$this->initialize();
	}
	protected function initialize()
	{
	}
	protected function validate(array $data, $validate, array $message = [], bool $batch = false)
	{
		if (is_array($validate)) {
			$v = new \think\Validate();
			$v->rule($validate);
		} else {
			if (strpos($validate, ".")) {
				list($validate, $scene) = explode(".", $validate);
			}
			$class = false !== strpos($validate, "\\") ? $validate : $this->app->parseClass("validate", $validate);
			$v = new $class();
			if (!empty($scene)) {
				$v->scene($scene);
			}
		}
		$v->message($message);
		if ($batch || $this->batchValidate) {
			$v->batch(true);
		}
		return $v->failException(true)->check($data);
	}
	protected function success($msg = "", string $url = null, $data = "", int $wait = 3, array $header = [])
	{
		if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
			$url = $_SERVER["HTTP_REFERER"];
		} else {
			if ($url) {
				$url = strpos($url, "://") || 0 === strpos($url, "/") ? $url : app("route")->buildUrl($url);
			}
		}
		$result = ["code" => 1, "msg" => $msg, "data" => $data, "url" => $url, "wait" => $wait];
		$type = $this->getResponseType();
		if ($type == "html") {
			$response = view(config("app.dispatch_success_tmpl"), $result);
		} elseif ($type == "json") {
			$response = json($result);
		}
		throw new \think\exception\HttpResponseException($response);
	}
	protected function error($msg = "", string $url = null, $data = "", int $wait = 3, array $header = [])
	{
		if (is_null($url)) {
			$url = $this->request->isAjax() ? "" : "javascript:history.back(-1);";
		} else {
			if ($url) {
				$url = strpos($url, "://") || 0 === strpos($url, "/") ? $url : $this->app->route->buildUrl($url);
			}
		}
		$result = ["code" => 0, "msg" => $msg, "data" => $data, "url" => $url, "wait" => $wait];
		$type = $this->getResponseType();
		if ($type == "html") {
			$response = view(config("app.dispatch_success_tmpl"), $result);
		} elseif ($type == "json") {
			$response = json($result);
		}
		throw new \think\exception\HttpResponseException($response);
	}
	protected function getResponseType()
	{
		return $this->request->isJson() || $this->request->isAjax() ? "json" : "html";
	}
	public function getRandChar($length)
	{
		$str = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol) - 1;
		for ($i = 0; $i < $length; $i++) {
			$str .= $strPol[rand(0, $max)];
		}
		return $str;
	}
	public function getSign($Obj, $api_key)
	{
		foreach ($Obj as $k => $v) {
			$Parameters[strtolower($k)] = $v;
		}
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		$String = $String . "&key=" . $api_key;
		$result_ = strtoupper(md5($String));
		return $result_;
	}
	public function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}
			$buff .= strtolower($k) . "=" . $v . "&";
		}
		if (strlen($buff) > 0) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		}
		return $reqPar;
	}
	public function arrayToXml($arr)
	{
		$xml = "<xml>";
		foreach ($arr as $key => $val) {
			if (is_numeric($val)) {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			} else {
				$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
			}
		}
		$xml .= "</xml>";
		return $xml;
	}
	public function curl($param = "", $url, $path_cert, $path_key)
	{
		$postUrl = $url;
		$curlPost = $param;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $postUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSLCERT, $path_cert);
		curl_setopt($ch, CURLOPT_SSLKEY, $path_key);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	public function xmlstr_to_array($xmlstr)
	{
		libxml_disable_entity_loader(true);
		$xmlstring = simplexml_load_string($xmlstr, "SimpleXMLElement", LIBXML_NOCDATA);
		$val = json_decode(json_encode($xmlstring), true);
		return $val;
	}
	public static function apiv3sign($url, $http_method, $body, $config)
	{
		$url_parts = parse_url($url);
		$canonical_url = $url_parts["path"] . (!empty($url_parts["query"]) ? "?{$url_parts["query"]}" : "");
		$timestamp = time();
		$nonce = self::getRandChar(32);
		$message = $http_method . "\n" . $canonical_url . "\n" . $timestamp . "\n" . $nonce . "\n" . $body . "\n";
		$priKey = file_get_contents($config["refund_key"]);
		openssl_sign($message, $raw_sign, $priKey, "sha256WithRSAEncryption");
		$sign = base64_encode($raw_sign);
		$header = ["Content-Type:application/json", "Accept:application/json", "User-Agent:*/*", "Authorization: " . sprintf("WECHATPAY2-SHA256-RSA2048 mchid=\"%s\",serial_no=\"%s\",timestamp=\"%s\",nonce_str=\"%s\",signature=\"%s\"", $config["mch_id"], $config["serial_no"], $timestamp, $nonce, $sign)];
		return $header;
	}
}