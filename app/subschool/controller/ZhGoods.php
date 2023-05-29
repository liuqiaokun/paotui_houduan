<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class ZhGoods extends Admin
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
			$where["a.goods_type_id"] = $this->request->param("goods_type_id", "", "serach_in");
			$where["a.business_id"] = $this->request->param("business_id", "", "serach_in");
			$where["a.goods_name"] = ["like", $this->request->param("goods_name", "", "serach_in")];
			$price_start = $this->request->param("price_start", "", "serach_in");
			$price_end = $this->request->param("price_end", "", "serach_in");
			$where["a.price"] = ["between", [$price_start, $price_end]];
			$where["a.status"] = $this->request->param("status", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["a.createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$sql = "select c.business_name,b.goods_type_name,a.* from gc_zh_goods a join gc_zh_business c on a.business_id=c.business_id join gc_zh_goods_type b on b.goods_type_id = a.goods_type_id";
			$limit = ($page - 1) * $limit . "," . $limit;
			$res = \xhadmin\CommonService::loadList($sql, formatWhere($where), $limit, $orderby);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			return view("add");
		} else {
			$postField = "goods_type_id,goods_name,price,goods_img,status,createtime,sort,stock,quota";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$data["s_id"] = session("subschool.s_id");
			$data["wxapp_id"] = session("subschool.wxapp_id");
			$data["createtime"] = time();
			$businessId = \think\facade\Db::name("zh_goods_type")->where(["wxapp_id" => $data["wxapp_id"], "s_id" => $data["s_id"], "goods_type_id" => $data["goods_type_id"]])->value("business_id");
			$data["business_id"] = $businessId;
			$res = \app\subschool\service\ZhGoodsService::add($data);
			return json(["status" => "00", "msg" => "添加成功"]);
		}
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$id = $this->request->get("id", "", "serach_in");
			if (!$id) {
				$this->error("参数错误");
			}
			$this->view->assign("info", checkData(\app\subschool\model\ZhGoods::find($id)));
			return view("update");
		} else {
			$postField = "id,goods_type_id,goods_name,price,goods_img,status,createtime,sort,stock,quota";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$businessId = \think\facade\Db::name("zh_goods_type")->where("goods_type_id", $data["goods_type_id"])->value("business_id");
			$data["business_id"] = $businessId;
			$res = \app\subschool\service\ZhGoodsService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhGoods::destroy(["id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
}