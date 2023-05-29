<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class ZhGoodsType extends Admin
{
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
			$where["a.business_id"] = $this->request->param("business_id", "", "serach_in");
			$where["a.goods_type_name"] = $this->request->param("goods_type_name", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["a.createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.business_name";
			$orderby = $sort && $order ? $sort . " " . $order : "goods_type_id desc";
			$res = \app\subschool\service\ZhGoodsTypeService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "business_id,goods_type_name,createtime,sort";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$data["wxapp_id"] = session("subschool.wxapp_id");
			$data["s_id"] = session("subschool.s_id");
			$data["create_time"] = time();
			$res = \app\subschool\service\ZhGoodsTypeService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$goods_type_id = $this->request->get("goods_type_id", "", "serach_in");
			if (!$goods_type_id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\ZhGoodsType::find($goods_type_id)));
			return view("update");
		} else {
			$postField = "goods_type_id,business_id,goods_type_name,createtime,sort";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$res = \app\subschool\service\ZhGoodsTypeService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("goods_type_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhGoodsType::destroy(["goods_type_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function view()
	{
		$goods_type_id = $this->request->get("goods_type_id", "", "serach_in");
		if (!$goods_type_id) {
			$this->error("参数错误");
		}
		$sql = "select a.*,b.business_name from gc_zh_goods_type as a left join gc_zh_business as b on a.business_id = b.business_id where a.goods_type_id = " . $goods_type_id . " limit 1";
		$info = \think\facade\Db::connect("mysql")->query($sql);
		$this->view->assign("info", current($info));
		return view("view");
	}
}