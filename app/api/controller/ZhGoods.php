<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhGoods extends Common
{
	public function index()
	{
		$wxapp_id = $this->request->get("wxapp_id");
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
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$businessId = \think\facade\Db::name("zh_business")->where(["wxadmin_name" => $uid, "wxapp_id" => $wxapp_id])->value("business_id");
		$where = [];
		$where["status"] = $this->request->get("status");
		$where["wxapp_id"] = $wxapp_id;
		$where["business_id"] = $businessId;
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\ZhGoodsService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$postField = "goods_type_id,goods_name,price,goods_img,status,specs,attribute,stock,quota,specs_list";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "wxadmin_name" => $uid])->value("business_id");
		$data["createtime"] = time();
		$data["business_id"] = $businessId;
		$data["wxapp_id"] = $wxapp_id;
		$data["s_id"] = $s_id;
		$judge = $this->msg_check($data["goods_name"], $data["wxapp_id"]);
		if (!$judge) {
			return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
		}
		$res = \app\api\service\ZhGoodsService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function update()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$postField = "id,goods_type_id,goods_name,price,goods_img,status,specs,attribute,stock,quota,specs_list";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (empty($data["id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["wxapp_id"] = $wxapp_id;
		$where["id"] = $data["id"];
		$res = \app\api\service\ZhGoodsService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function goodShelves()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$postField = "status,id";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$res = \think\facade\Db::name("zh_goods")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "id" => $data["id"]])->update(["status" => $data["status"]]);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function view()
	{
		$wxapp_id = $this->request->get("wxapp_id");
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
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$data["id"] = $this->request->get("id");
		$data["wxapp_id"] = $wxapp_id;
		$field = "*";
		$res = checkData(\app\api\model\ZhGoods::field($field)->where($data)->find());
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function getBusinessGoods()
	{
		$wxapp_id = $this->request->get("wxapp_id");
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
		$businessId = $this->request->get("business_id");
		$type = $this->request->get("type");
		$where = ["business_id" => $businessId, "wxapp_id" => $wxapp_id, "s_id" => $s_id];
		$businessData = \think\facade\Db::name("zh_business")->where($where)->find();
		$start = strtotime(date("H:i", time()));
		$start_time = strtotime($businessData["start_time"]);
		$end_time = strtotime($businessData["end_time"]);
		if ($start_time <= $start && $start <= $end_time) {
			$time_status = 1;
		} else {
			$time_status = 0;
		}
		$businessTypeData = \think\facade\Db::name("zh_goods_type")->where($where)->order("sort", "desc")->select()->toArray();
		foreach ($businessTypeData as &$v) {
			$v["titleId"] = "title" . $v["goods_type_id"];
			$v["foodCount"] = 0;
			$goodsData = \think\facade\Db::name("zh_goods")->where($where)->where(["goods_type_id" => $v["goods_type_id"], "status" => $type])->order("sort", "desc")->select()->toArray();
			foreach ($goodsData as &$val) {
				$val["count"] = 0;
				$val["foodId"] = $val["id"];
				$val["specs"] = json_decode(html_entity_decode($val["specs"]), true);
				$val["attribute"] = json_decode(html_entity_decode($val["attribute"]), true);
				$val["specs_list"] = json_decode(html_entity_decode($val["specs_list"]), true);
			}
			unset($val);
			$v["items"] = $goodsData;
		}
		unset($v);
		$sale_num = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("store_id", $businessData["business_id"])->where("status", 4)->count();
		$businessData["sale_num"] = $sale_num + $businessData["virtual_sale"];
		$timeslot = explode("|", $businessData["timeslot"]);
		$timess = strtotime(date("H:i", time()));
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
		if ($start_time <= $start && $start <= $end_time && $businessData["status"] == 1) {
			$businessData["is_open"] = 1;
		} else {
			$businessData["is_open"] = 0;
		}
		$result["goods"] = $businessTypeData;
		$result["status"] = $businessData["status"];
		$result["time_status"] = $time_status;
		$result["starting_fee"] = $businessData["starting_fee"];
		$result["info"] = $businessData;
		return $this->ajaxReturn($this->successCode, "返回成功", $result);
	}
	public function sumMoney()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$store_id = $this->request->post("store_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$store = \think\facade\Db::name("zh_business")->find($store_id);
		$a = $_POST["goods_det"];
		$json = json_decode($a, true);
		$total_prices = 0;
		foreach ($json as $k => $v) {
			$information = \think\facade\Db::name("zh_goods")->alias("a")->join("zh_goods_type b ", "b.goods_type_id= a.goods_type_id")->join("zh_business c", "c.business_id = a.business_id")->where(["a.wxapp_id" => $wxapp_id, "a.s_id" => $s_id, "a.id" => $v["ids"]])->find();
			$specs = json_decode(html_entity_decode($information["specs"]), true);
			$specs_list = json_decode(html_entity_decode($information["specs_list"]), true);
			$json[$k]["goods_name"] = $information["goods_name"];
			if ($v["specs"] && count($specs["list"]) > 0) {
				$json[$k]["goods_name"] .= "[" . $v["specs"];
				foreach ($specs["list"] as &$v1) {
					$specs_price = 0;
					if ($v["specs"] == $v1["type"]) {
						$specs_price += $v1["price"];
						if (count($v["specss"] > 0)) {
							foreach ($v["specss"] as $k2 => $v2) {
								if ($v2["type"]) {
									$json[$k]["goods_name"] .= "、" . $v2["type"];
									foreach ($specs_list[$k2]["list"] as $k3 => $v3) {
										if ($v3["type"] == $v2["type"]) {
											$specs_price += $v3["price"];
										}
									}
								}
							}
						}
						$total_prices += $v["nums"] * $specs_price;
						$json[$k]["price"] = $specs_price;
						$json[$k]["sum_price"] = round($v["nums"] * $specs_price, 2);
					}
				}
				$json[$k]["goods_name"] .= "]";
			} else {
				$total_prices += $v["nums"] * $information["price"];
				$json[$k]["price"] = $information["price"];
				$json[$k]["sum_price"] = round($v["nums"] * $information["price"], 2);
			}
			$json[$k]["cate_name"] = $information["goods_type_name"];
			$json[$k]["store_name"] = $information["business_name"];
			$json[$k]["image"] = $information["goods_img"];
		}
		$data = ["data" => $json, "total_prices" => round($total_prices + $store["service_price"] + $store["box_fee"], 2), "service_price" => $store["service_price"], "box_fee" => $store["box_fee"]];
		return $this->ajaxReturn($this->successCode, "操作成功", $data);
	}
	public function sumMoney_yuan()
	{
		$wxapp_id = $this->request->get("wxapp_id");
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
		$a = $_GET["goods_det"];
		$json = json_decode($a, true);
		$total_prices = 0;
		foreach ($json as $k => $v) {
			$information = \think\facade\Db::name("zh_goods")->alias("a")->join("zh_goods_type b ", "b.goods_type_id= a.goods_type_id")->join("zh_business c", "c.business_id = a.business_id")->where(["a.wxapp_id" => $wxapp_id, "a.s_id" => $s_id, "a.id" => $v["ids"]])->find();
			$json[$k]["goods_name"] = $information["goods_name"];
			$json[$k]["price"] = $information["price"];
			$json[$k]["cate_name"] = $information["goods_type_name"];
			$json[$k]["store_name"] = $information["business_name"];
			$json[$k]["sum_price"] = round($v["nums"] * $information["price"], 2);
			$total_prices += $v["nums"] * $information["price"];
		}
		$data = ["data" => $json, "total_prices" => round($total_prices, 2)];
		return $this->ajaxReturn($this->successCode, "操作成功", $data);
	}
	public function getcommodity()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		$s_id = $this->request->get("s_id");
		$business = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id])->field("business_id")->select()->toArray();
		$data = [];
		foreach ($business as $key => $v) {
			$data[$key] = $v;
			$data[$key]["sale_num"] = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("store_id", $v["business_id"])->where("status", 4)->count();
		}
		$tags_count = array_column($data, "sale_num");
		array_multisort($tags_count, SORT_DESC, $data);
		$datas = [];
		for ($i = 0; $i < 5; $i++) {
			$datas[$i] = $data[$i];
			$datas[$i]["goods"] = \think\facade\Db::name("zh_goods")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id])->where("business_id", $data[$i]["business_id"])->orderRaw("rand()")->find();
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $datas);
	}
}