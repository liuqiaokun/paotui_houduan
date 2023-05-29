<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhBusinesType extends Common
{
	public function index()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		$uid = $this->request->uid;
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$limit = $this->request->get("limit", 100, "intval");
		$page = $this->request->get("page", 1, "intval");
		$store = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "wxadmin_name" => $uid])->find();
		$where = [];
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$field = "*";
		$orderby = "sort desc";
		$res = \app\api\service\ZhBusinesTypeService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function getBusiness()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		$latitude = $this->request->get("latitude", 0);
		$longitude = $this->request->get("longitude", 0);
		$kwd = $this->request->get("kwd");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$typeId = $this->request->get("type_id");
		$limit = $this->request->get("limit", 10, "intval");
		$page = $this->request->get("page", 1, "intval");
		$field = "*";
		$orderby = "type_id desc";
		$where = [];
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$condition = " where s_id = " . $s_id;
		$condition .= " and wxapp_id = " . $wxapp_id;
		if ($sSetting["store_bean_switch"] == 1) {
			$condition .= " and balance > 0";
		}
		if ($kwd) {
			$condition .= " and business_name  like '%" . $kwd . "%'";
		}
		$condition .= " and status = 1 ";
		if ($typeId) {
			$where["type_id"] = $typeId;
			$condition .= " and type_id = " . $typeId;
			$res["data"] = \think\facade\Db::query("SELECT *,ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN(( " . $latitude . " * PI() / 180 - latitude * PI() / 180) / 2), 2) + COS(" . $latitude . " * PI() / 180) * COS(latitude * PI() / 180) * POW(SIN((" . $longitude . " * PI() / 180 - longitude * PI() / 180) / 2 ),2 )))) AS juli FROM gc_zh_business " . $condition . " ORDER BY sort desc,juli asc limit " . ($page - 1) * $limit . "," . $limit);
		} else {
			$res["data"] = \think\facade\Db::query("SELECT *,ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN(( " . $latitude . " * PI() / 180 - latitude * PI() / 180) / 2), 2) + COS(" . $latitude . " * PI() / 180) * COS(latitude * PI() / 180) * POW(SIN((" . $longitude . " * PI() / 180 - longitude * PI() / 180) / 2 ),2 )))) AS juli FROM gc_zh_business " . $condition . " ORDER BY sort desc,juli asc limit " . ($page - 1) * $limit . "," . $limit);
		}
		$timess = strtotime(date("H:i", time()));
		foreach ($res["data"] as &$v) {
			$sale_num = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("store_id", $v["business_id"])->where("status", 4)->count();
			$v["sale_num"] = $sale_num + $v["virtual_sale"];
			$start = strtotime(date("H:i", time()));
			$start_time = strtotime($v["start_time"]);
			$end_time = strtotime($v["end_time"]);
			$timeslot = explode("|", $v["timeslot"]);
			$v["goods"] = \think\facade\Db::name("zh_goods")->where("business_id", $v["business_id"])->orderRaw("rand()")->limit(3)->select()->toArray();
			$times = [];
			foreach ($timeslot as $ks => $vs) {
				$nowtime = explode("-", $vs);
				$starts = strtotime($nowtime[0]);
				$ends = strtotime($nowtime[1]);
				if ($starts <= $timess && $timess <= $ends) {
					$times[$ks] = $vs;
				}
			}
			$times = array_merge($times);
			$nowtime = explode("-", $times[0]);
			$start_time = strtotime($nowtime[0]);
			$end_time = strtotime($nowtime[1]);
			if ($start_time <= $start && $start <= $end_time && $v["status"] == 1) {
				$v["is_open"] = 1;
			} else {
				$v["is_open"] = 0;
			}
		}
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}