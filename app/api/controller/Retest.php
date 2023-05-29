<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Retest extends Common
{
	public function index()
	{
		$time = "11:00:00 - 13:00:00";
		if (date("H:i") > $time[1]) {
			echo "guoqi";
		}
		print_r(explode(" - ", $time));
		exit;
		$redis = new \think\cache\driver\Redis();
		$redis->connect("127.0.0.1", 6379);
		$redis_name = date("Y-m-d") . "id" . "1";
		$count = $redis->lLen($redis_name);
		$redis->set($redis_name, 200);
		print_r($redis->get($redis_name));
		exit;
		$kuchun = $redis->get("kucun");
		$total = 102;
		if ($kuchun < $total) {
			$redis->watch("kucun");
			$redis->multi();
			$redis->set("kucun", $kuchun + 1);
			$result = $redis->exec();
			if ($result) {
				$number = $total - ($kuchun + 1);
				$openid = $number;
				$redis->hset("list", "user_" . $openid, $kuchun);
				$data = $redis->hgetall("list");
				print_r($data);
				print_r($number);
			} else {
				var_dump("手气很差哦，再试一下！");
			}
		} else {
			var_dump("已经被抢光了");
		}
	}
}