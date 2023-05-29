<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class School extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 200, "intval");
		$page = $this->request->post("page", 1, "intval");
		$kwd = $this->request->post("kwd");
		$city = $this->request->post("city");
		$where = [];
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		if ($kwd) {
			$where["s_name"] = ["like", "%" . $kwd . "%"];
		}
		if ($city) {
			$where["city"] = $city;
		}
		$where["status"] = 1;
		$field = "*";
		$orderby = "s_id desc";
		$res = \app\api\service\SchoolService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function view()
	{
		$data["s_id"] = $this->request->post("s_id", "", "serach_in");
		$wxapp_id = $this->request->post("wxapp_id");
		$show_type = $this->request->post("show_type");
		$latitude = $this->request->post("latitude", 0);
		$longitude = $this->request->post("longitude", 0);
		$res = checkData(\app\api\model\School::find($data["s_id"]));
		$modules = \think\facade\Db::name("dmh_modular")->order("sort", "desc")->where("s_id", $data["s_id"])->select();
		$slide = \think\facade\Db::name("slide")->where("s_id", $data["s_id"])->where("show_type", $show_type)->select();
		if (count($slide) == 0) {
			$slide = [["img" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/banners.jpg", "url_type" => 0]];
		}
		if (count($modules) == 0) {
			$modules = [["id" => 0, "title" => "取快递", "image" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/modules/qu_icon.png", "types" => 0, "appid" => " "], ["id" => 0, "title" => "寄快递", "image" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/modules/ji_icon.png", "types" => 0, "appid" => " "], ["id" => 0, "title" => "食堂超市", "image" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/modules/shi_icon.png", "types" => 0, "appid" => " "], ["id" => 0, "title" => "万能任务", "image" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/modules/wan_icon.png", "types" => 0, "appid" => " "]];
		}
		$article_cate = \think\facade\Db::name("zh_forum_class")->where("s_id", $data["s_id"])->where("is_recommend", 1)->order("sort", "desc")->select();
		if (count($article_cate) == 0) {
			$article_cate = [["class_name" => "游戏代练", "img" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/youxi.png"], ["class_name" => "影视摄影", "img" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/yingshi.png"], ["class_name" => "吃货联盟", "img" => "https://" . $_SERVER["HTTP_HOST"] . "/wximages/chihuo.png"]];
		}
		$condition = $res["store_bean_switch"] == 1 ? " and is_recommend = 1 and balance > 0 " : " and is_recommend = 1 ";
		$res["store"] = \think\facade\Db::query("SELECT *,ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN(( " . $latitude . " * PI() / 180 - latitude * PI() / 180) / 2), 2) + COS(" . $latitude . " * PI() / 180) * COS(latitude * PI() / 180) * POW(SIN((" . $longitude . " * PI() / 180 - longitude * PI() / 180) / 2 ),2 )))) AS juli FROM gc_zh_business WHERE s_id = " . $data["s_id"] . $condition . "  ORDER BY sort desc,juli asc ");
		$stores = [];
		foreach ($res["store"] as $keys => $vs) {
			$stores[$keys] = $vs;
			$stores[$keys]["goods"] = \think\facade\Db::name("zh_goods")->where("business_id", $vs["business_id"])->orderRaw("rand()")->limit(3)->select();
		}
		$res["store"] = $stores;
		$res["slide"] = $slide;
		$res["modules"] = $modules;
		$res["article_cate"] = $article_cate;
		$res["default_store_module"] = \think\facade\Db::name("dmh_modular")->where("s_id", $data["s_id"])->where("appid", "like", "%/gc_school/pages/canteen/canteen%")->order("id", "desc")->find();
		$res["notice"] = \think\facade\Db::name("notice")->where(["wxapp_id" => $wxapp_id, "s_id" => $data["s_id"]])->select();
		$timess = strtotime(date("H:i", time()));
		foreach ($res["store"] as $k => $v) {
			$start = strtotime(date("H:i", time()));
			$sale_num = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("store_id", $v["business_id"])->where("status", 4)->count();
			$v["sale_num"] = $sale_num + $v["virtual_sale"];
			$res["store"][$k] = $v;
			$timeslot = explode("|", $v["timeslot"]);
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
				$res["store"][$k]["is_open"] = 1;
			} else {
				$res["store"][$k]["is_open"] = 0;
			}
		}
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function noticeDetail()
	{
		$id = $this->request->post("id");
		$data = \think\facade\Db::name("notice")->find($id);
		return $this->ajaxReturn($this->successCode, "返回成功", $data);
	}
	public function changeSchool()
	{
		$s_id = $this->request->post("s_id");
		$u_id = $this->request->uid;
		if ($u_id) {
			$res = \think\facade\Db::name("wechat_user")->where("u_id", $u_id)->update(["s_id" => $s_id]);
			return $this->ajaxReturn($this->successCode, "操作成功", $res);
		}
	}
	public function info()
	{
		$data["s_id"] = $this->request->post("s_id", "", "serach_in");
		$res = checkData(\app\api\model\School::find($data["s_id"]));
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
	public function template()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$data = \think\facade\Db::name("template")->where("wxapp_id", $wxapp_id)->find();
		$data["type"] = $data ? $data["type"] : 1;
		return $this->ajaxReturn($this->successCode, "返回成功", $data);
	}
	public function address()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		$school = \think\facade\Db::name("school")->where("wxapp_id", $wxapp_id)->find();
		$data = explode("|", $school["floor"]);
		return $this->ajaxReturn($this->successCode, "返回成功", $data);
	}
	public function stotelist()
	{
		$wxapp_id = $this->request->post("wxapp_id", "", "serach_in");
		$limit = $this->request->post("limit", 10, "intval");
		$page = $this->request->post("page", 1, "intval");
		$data["s_id"] = $this->request->post("s_id", "", "serach_in");
		$latitude = $this->request->post("latitude", 0);
		$longitude = $this->request->post("longitude", 0);
		$res = checkData(\app\api\model\School::find($data["s_id"]));
		$store = \think\facade\Db::query("SELECT *,ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN(( " . $latitude . " * PI() / 180 - latitude * PI() / 180) / 2), 2) + COS(" . $latitude . " * PI() / 180) * COS(latitude * PI() / 180) * POW(SIN((" . $longitude . " * PI() / 180 - longitude * PI() / 180) / 2 ),2 )))) AS juli FROM gc_zh_business WHERE s_id = " . $data["s_id"] . "  ORDER BY sort desc,juli asc limit " . ($page - 1) * $limit . "," . $limit);
		$stores = [];
		foreach ($store as $keys => $vs) {
			$stores[$keys] = $vs;
			$stores[$keys]["goods"] = \think\facade\Db::name("zh_goods")->where("business_id", $vs["business_id"])->orderRaw("rand()")->limit(3)->select();
		}
		$timess = strtotime(date("H:i", time()));
		foreach ($stores as $k => $v) {
			$start = strtotime(date("H:i", time()));
			$sale_num = \think\facade\Db::name("dmh_school_order")->where("wxapp_id", $wxapp_id)->where("store_id", $v["business_id"])->where("status", 4)->count();
			$v["sale_num"] = $sale_num + $v["virtual_sale"];
			$stores[$k] = $v;
			$timeslot = explode("|", $v["timeslot"]);
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
				$stores[$k]["is_open"] = 1;
			} else {
				$stores[$k]["is_open"] = 0;
			}
		}
		return $this->ajaxReturn($this->successCode, "返回成功", $stores);
	}
}