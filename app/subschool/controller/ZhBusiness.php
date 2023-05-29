<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class ZhBusiness extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["updateExt", "update", "delete", "view"])) {
			$idx = $this->request->param("business_id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\subschool\model\ZhBusiness::find($v);
					if ($info["wxapp_id"] != session("subschool.wxapp_id")) {
						$this->error("你没有操作权限");
					}
				}
			}
		}
	}
	public function index()
	{
		if (!$this->request->isAjax()) {
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["a.s_id"] = session("subschool.s_id");
			$where["a.wxapp_id"] = session("subschool.wxapp_id");
			$where["a.type_id"] = $this->request->param("type_id", "", "serach_in");
			$where["a.business_name"] = $this->request->param("business_name", "", "serach_in");
			$where["a.business_address"] = $this->request->param("business_address", "", "serach_in");
			$where["a.phone"] = $this->request->param("phone", "", "serach_in");
			$where["a.status"] = $this->request->param("status", "", "serach_in");
			$where["a.type"] = $this->request->param("type", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["a.createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.type_name";
			$orderby = $sort && $order ? $sort . " " . $order : "business_id desc";
			$res = \app\subschool\service\ZhBusinessService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function updateExt()
	{
		$postField = "business_id,is_recommend,is_dormitory_store";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (!$data["business_id"]) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhBusiness::update($data);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "s_id,clientid,deliveryfee,clientsecret,machine_code,msign,printtype,wxapp_id,timeslot,wxadmin_name,type_id,start_time,end_time,business_name,business_address,phone,expire_time,business_image,status,type,createtime,is_recommend,starting_fee,longitude,latitude,printer,printer_user,printer_ukey,virtual_sale,service_price,box_fee,delivery_fee,store_money,fx_store_money,is_dormitory_store,sort";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$s_id = session("subschool.s_id");
			$school = $this->app->db->name("school")->where("s_id", $s_id)->find();
			if ($school["fx_store_rate"] < $data["fx_store_money"]) {
				$msg = "抽成不能大于百分之" . $school["fx_store_rate"];
				$this->error($msg);
			}
			$res = \app\subschool\service\ZhBusinessService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$business_id = $this->request->get("business_id", "", "serach_in");
			if (!$business_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\ZhBusiness::find($business_id)));
			return view("update");
		} else {
			$postField = "business_id,clientid,deliveryfee,clientsecret,machine_code,msign,printtype,timeslot,wxadmin_name,type_id,start_time,end_time,business_name,business_address,phone,expire_time,business_image,status,type,is_recommend,starting_fee,longitude,latitude,printer,printer_user,printer_ukey,virtual_sale,service_price,box_fee,delivery_fee,store_money,fx_store_money,is_dormitory_store,sort";
			$data = $this->request->only(explode(",", $postField), "post", null);
			if (!$data["delivery_fee"]) {
				$data["delivery_fee"] = null;
			}
			$user = $this->app->db->name("zh_business")->where("business_id", $data["business_id"])->find();
			$business = $this->app->db->name("zh_business")->where("wxadmin_name", $data["wxadmin_name"])->find();
			if ($user["wxadmin_name"] != $data["wxadmin_name"]) {
				$this->users($user["wxadmin_name"], $data["wxadmin_name"]);
			}
			$s_id = session("subschool.s_id");
			$school = $this->app->db->name("school")->where("s_id", $s_id)->find();
			if ($school["fx_store_rate"] < $data["fx_store_money"]) {
				$msg = "抽成不能大于百分之" . $school["fx_store_rate"];
				$this->error($msg);
			}
			$res = \app\subschool\service\ZhBusinessService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function users($primary, $present)
	{
		$settled = $this->app->db->name("dmh_settled")->where("u_id", $primary)->find();
		$this->app->db->name("dmh_settled")->where("d_id", $settled["d_id"])->update(["u_id" => $present]);
	}
	public function addbean()
	{
		if (!$this->request->isPost()) {
			$business_id = $this->request->get("business_id", "", "serach_in");
			if (!$business_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\ZhBusiness::find($business_id)));
			return view("addbean");
		} else {
			$business_id = $this->request->post("business_id");
			$num = $this->request->post("num");
			$info = \app\subschool\model\ZhBusiness::find($business_id);
			\think\facade\Db::startTrans();
			$insert = ["wxapp_id" => $info["wxapp_id"], "bus_id" => $info["business_id"], "o_id" => 99999999, "type" => 1, "number" => $num];
			$res = \think\facade\Db::name("business_account_log")->insert($insert);
			$res1 = \think\facade\Db::name("zh_business")->where("business_id", $info["business_id"])->inc("balance", $num)->update();
			if ($res && $res1) {
				\think\facade\Db::commit();
				return json(["status" => "00", "msg" => "操作成功"]);
			} else {
				\think\facade\Db::rollback();
				return json(["status" => "01", "msg" => "操作失败"]);
			}
		}
	}
	public function delete()
	{
		$idx = $this->request->post("business_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			$info = \think\facade\Db::name("zh_business")->find($idx);
			$settle = \think\facade\Db::name("dmh_settled")->where("u_id", $info["wxadmin_name"])->find();
			if ($settle) {
				\think\facade\Db::name("dmh_settled")->where("d_id", $settle["d_id"])->update(["u_id" => 0]);
			}
			\app\subschool\model\ZhBusiness::destroy(["business_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function view()
	{
		$business_id = $this->request->get("business_id", "", "serach_in");
		if (!$business_id) {
			$this->error("参数错误");
		}
		$sql = "select a.*,b.type_name from gc_zh_business as a left join gc_zh_business_type as b on a.type_id = b.type_id where a.business_id = " . $business_id . " limit 1";
		$info = \think\facade\Db::connect("mysql")->query($sql);
		$this->view->assign("info", current($info));
		return view("view");
	}
}