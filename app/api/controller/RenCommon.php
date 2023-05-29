<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class RenCommon extends Common
{
	public function TimeList()
	{
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
		$time = time();
		foreach ($date_time[0]["times"] as $k => $v) {
			$tg = strtotime(date("Y-m-d", time()) . " " . $v);
			if ($tg < $time) {
				unset($date_time[0]["times"][$k]);
			}
		}
		$now = ["2小时内", "当天送达"];
		$date_time[0]["times"] = array_merge($now, $date_time[0]["times"]);
		if (empty($date_time[0]["times"])) {
			unset($date_time[0]);
		}
		$data["time"] = $date_time;
		return $this->ajaxReturn($this->successCode, "返回成功", $data);
	}
}