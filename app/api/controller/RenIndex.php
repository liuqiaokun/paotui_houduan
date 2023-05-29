<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class RenIndex extends Common
{
	public function testLogin()
	{
		print_r(session_regenerate_id());
		echo "====";
		print_r(session_id());
		exit;
	}
	public function tests()
	{
		$data = \think\facade\Db::name("zh_business")->find(20);
		$temp = $data["fx_store_money"] != null ? $data["fx_store_money"] : "ttl";
		print_r($temp);
		exit;
		exit;
		$where[] = ["createtime", "between", [strtotime(date("Y-m-d")), strtotime(date("Y-m-d", strtotime("+1 day")))]];
		$where[] = ["store_id", "=", 22];
		$data = \think\facade\Db::name("dmh_school_order")->where($where)->count();
		print_r($data);
		exit;
	}
	public function TimeList()
	{
		$type = $this->request->post("type");
		$t1 = date("m-d", time());
		$date_time[]["day"] = $t1;
		$t2 = date("m-d", strtotime("+1 day"));
		$date_time[]["day"] = $t2;
		$t3 = date("m-d", strtotime("+2 day"));
		$date_time[]["day"] = $t3;
		$t4 = date("m-d", strtotime("+3 day"));
		$start_time = strtotime(date("Y-m-d", time()));
		$beginToday = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$endToday = mktime(24, 0, 0, date("m"), date("d"), date("Y"));
		while ($beginToday <= $endToday) {
			$date_times[] = date("H:i", $beginToday);
			$beginToday += 1200;
		}
		foreach ($date_time as $key => &$val) {
			$val["times"] = $date_times;
		}
		$time = strtotime("+20 minutes");
		foreach ($date_time[0]["times"] as $k => $v) {
			$tg = strtotime(date("Y-m-d", time()) . " " . $v);
			if ($tg < $time) {
				unset($date_time[0]["times"][$k]);
			}
		}
		$now = $type == 3 ? ["立即"] : ["当天送达", "2小时内"];
		$date_time[0]["times"] = array_merge($now, $date_time[0]["times"]);
		if (empty($date_time[0]["times"])) {
			unset($date_time[0]);
		}
		$data["time"] = $date_time;
		return $this->ajaxReturn($this->successCode, "返回成功", $data);
	}
	public function givetime()
	{
		$s_id = $this->request->post("s_id");
		$school = $this->app->db->name("school")->where("s_id", $s_id)->find();
		$ordertime = explode("|", $school["ordertime"]);
		$times = [];
		$time = strtotime(date("H:i", time()));
		foreach ($ordertime as $k => $v) {
			$nowtime = explode("-", $v);
			$start = strtotime($nowtime[0]);
			$end = strtotime($nowtime[1]);
			if ($time < $start && $time < $end) {
				$times[$k] = $v;
			}
		}
		$ordertime = array_merge($times);
		return $this->ajaxReturn($this->successCode, "返回成功下单时间段成功", $ordertime);
	}
	public function MinRunPrice()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$s_id = $this->request->post("s_id");
		$module_id = $this->request->post("module_id");
		$store_id = $this->request->post("store_id");
		if (empty($s_id)) {
			return $this->ajaxReturn($this->errorCode, "请先选择学校");
		}
		if ($module_id == 0) {
			$info = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		} else {
			$info = \think\facade\Db::name("dmh_modular")->find($module_id);
			$info["start_fee"] = $info["start"];
			$info["step_price"] = $info["ladder"];
		}
		if ($store_id) {
			$store = \think\facade\Db::name("zh_business")->find($store_id);
			$info["start_fee"] = $store["delivery_fee"] ? $store["delivery_fee"] : $info["start_fee"];
		}
		$school = \think\facade\Db::name("school")->find($s_id);
		$school["step"] = $school["step"] ? explode(";", $school["step"]) : ["小于5斤", "5~10斤", "10~20斤", "20~50斤"];
		$school["specs"] = \think\facade\Db::name("express_specs")->where("s_id", $s_id)->select();
		$point = \think\facade\Db::name("dmh_express")->where("s_id", $s_id)->select();
		$data["info"] = $info;
		$data["point"] = $point;
		$data["school"] = $school;
		return $this->ajaxReturn($this->successCode, "返回成功", $data);
	}
	private function httpGet($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		$res = curl_exec($curl);
		curl_close($curl);
		return $res;
	}
	public function getPhone()
	{
		include "wxBizDataCrypt.php";
		$wxapp_id = $this->request->post("wxapp_id");
		$code = $this->request->post("code");
		$encryptedData = $this->request->post("encryptedData");
		$iv = $this->request->post("iv");
		$config = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $config["appid"] . "&secret=" . $config["appsecret"] . "&js_code={$code}&grant_type=authorization_code";
		$result = json_decode($this->httpGet($url), true);
		$sessionKey = $result["session_key"];
		$pc = new \WXBizDataCrypt($config["appid"], $sessionKey);
		$errCode = $pc->decryptData($encryptedData, $iv, $data);
		if ($errCode == 0) {
			$data = json_decode($data, true);
			return $this->ajaxReturn($this->successCode, "获取手机号成功", $data);
		} else {
			$data = [];
			return $this->ajaxReturn($this->errorCode, "获取手机号失败", $data);
		}
	}
	public function calculatePrice1()
	{
		$postField = "express_num,food_money,express_info,weight,goods_det,store_id,y_money,input_price,is_vip,s_id,wxapp_id,module_id,store_id,type";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$express_info = json_decode(html_entity_decode($data["express_info"]), true);
		$express_sum = 0;
		foreach ($express_info as &$v) {
			$express_sum += $v["price"];
		}
		if ($data["module_id"] == 0) {
			$info = \think\facade\Db::name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
			$start_fee = $info["start_fee"];
		} else {
			$info = \think\facade\Db::name("dmh_modular")->find($data["module_id"]);
			$start_fee = $info["start"];
		}
		if ($data["store_id"]) {
			$store = \think\facade\Db::name("zh_business")->find($data["store_id"]);
			$start_fee = $store["delivery_fee"] ? $store["delivery_fee"] : $start_fee;
		}
		$jian = $start_fee;
		$res["start_fee"] = $start_fee;
		$start_fee = $data["input_price"] ? $data["input_price"] : $start_fee;
		$res["total"] = round($start_fee + $express_sum, 2);
		$start_fee = $data["is_vip"] == 1 ? round($start_fee - $jian, 2) : $start_fee;
		$res["food_money"] = round($data["food_money"], 2);
		$res["y_money"] = round($data["y_money"], 2);
		$res["is_vip"] = $data["is_vip"];
		$res["input_price"] = $data["input_price"];
		$res["t_money"] = round($start_fee + $data["food_money"] + $express_sum, 2);
		if ($res["y_money"] > $res["t_money"]) {
			return $this->ajaxReturn($this->errorCode, "优惠券不可用", 1);
		}
		$res["t_money"] = round($start_fee + $data["food_money"] - $data["y_money"] + $express_sum, 2);
		$unique = "GCWL" . date("YmdHis") . rand(100, 999) . rand(10, 99) . rand(1, 9);
		$res["unique"] = $unique;
		$res["express_info"] = json_encode($express_info);
		\think\facade\Cache::set($unique, $res, 7200);
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function calculatePrice()
	{
		$postField = "express_num,food_money,express_info,weight,goods_det,store_id,y_money,input_price,is_vip,s_id,wxapp_id,module_id,store_id,type";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$express_info = json_decode(html_entity_decode($data["express_info"]), true);
		$express_sum = 0;
		foreach ($express_info as &$v) {
			$express_sum += $v["price"];
		}
		if ($data["module_id"] == 0) {
			$info = \think\facade\Db::name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
			$start_fee = $info["start_fee"];
			$step_price = $info["step_price"];
		} else {
			$info = \think\facade\Db::name("dmh_modular")->find($data["module_id"]);
			$start_fee = $info["start"];
			$step_price = $info["ladder"];
		}
		if ($data["store_id"]) {
			$store = \think\facade\Db::name("zh_business")->find($data["store_id"]);
			$start_fee = $store["delivery_fee"] ? $store["delivery_fee"] : $start_fee;
		}
		$jian = $start_fee;
		$res["start_fee"] = $start_fee;
		$start_fee = $data["input_price"] ? $data["input_price"] : $start_fee;
		$res["total"] = round($start_fee + ($data["express_num"] - 1 + $data["weight"]) * $step_price + $express_sum, 2);
		$start_fee = $data["is_vip"] == 1 ? round($start_fee - $jian, 2) : $start_fee;
		$res["food_money"] = round($data["food_money"], 2);
		$res["y_money"] = round($data["y_money"], 2);
		$res["is_vip"] = $data["is_vip"];
		$res["input_price"] = $data["input_price"];
		$res["t_money"] = round($start_fee + ($data["express_num"] - 1 + $data["weight"]) * $step_price + $data["food_money"] + $express_sum, 2);
		if ($res["y_money"] > $res["t_money"]) {
			return $this->ajaxReturn($this->errorCode, "优惠券不可用", 1);
		}
		$res["t_money"] = round($start_fee + ($data["express_num"] - 1 + $data["weight"]) * $step_price + $data["food_money"] - $data["y_money"] + $express_sum, 2);
		$unique = "GCWL" . date("YmdHis") . rand(100, 999) . rand(10, 99) . rand(1, 9);
		$res["unique"] = $unique;
		$res["express_info"] = json_encode($express_info);
		\think\facade\Cache::set($unique, $res, 7200);
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function self_calculatePrice()
	{
		$postField = "express_num,food_money,express_info,weight,goods_det,store_id,y_money,input_price,is_vip,s_id,wxapp_id,module_id,store_id,type,is_self_lifting";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$res["total"] = 0;
		$res["t_money"] = round($data["food_money"], 2);
		$res["food_money"] = round($data["food_money"], 2);
		$res["y_money"] = 0;
		$unique = "GCWL" . date("YmdHis") . rand(100, 999) . rand(10, 99) . rand(1, 9);
		$res["unique"] = $unique;
		\think\facade\Cache::set($unique, $res, 7200);
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function student()
	{
		$uid = $this->request->uid;
		$data = \think\facade\Db::name("dmh_student")->where("u_id", $uid)->find();
		$result["status"] = empty($data) ? 0 : 1;
		$result["data"] = $data;
		return $this->ajaxReturn($this->successCode, "返回成功", $result);
	}
	public function testtest()
	{
		$val = $this->request->post("val");
		$val = json_decode(html_entity_decode($val), true);
		$sum = 0;
		foreach ($val as &$v) {
			$sum += $v["price"];
		}
		print_r($sum);
		exit;
	}
	public function batches()
	{
		$wxapp_id = input("wxapp_id");
		$config = \think\facade\Db::name("setting")->where("wxapp_id", 12)->find();
		$dataList = \think\facade\Db::name("user_withdraw")->where("wxapp_id", $wxapp_id)->where("status", 4)->select();
		foreach ($dataList as &$v) {
			$url = "https://api.mch.weixin.qq.com/v3/transfer/batches/batch-id/" . $v["batch_id"] . "?need_query_detail=true&detail_status=ALL";
			$result = \app\accounts\controller\HttpExtend::get($url, "", ["headers" => \app\BaseController::qian($url, "GET", "", $config)]);
			$result = json_decode($result, true);
			if ($result["transfer_batch"]["batch_status"] == "FINISHED" && $result["transfer_detail_list"][0]["detail_status"] == "SUCCESS") {
				\think\facade\Db::name("user_withdraw")->where("batch_id", $v["batch_id"])->update(["status" => 2]);
			}
		}
	}
}