<?php

//decode by http://www.yunlu99.com/
namespace app\subschool\controller;

class ZhArticles extends Admin
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
			$where["a.delete_time"] = ["exp", "is null"];
			$where["a.s_id"] = session("subschool.s_id");
			$where["a.wxapp_id"] = session("subschool.wxapp_id");
			$where["a.class_id"] = $this->request->param("class_id", "", "serach_in");
			$where["a.media_type"] = $this->request->param("media_type", "", "serach_in");
			$where["a.status"] = $this->request->param("status", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["a.createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "a.*,b.class_name";
			$orderby = $sort && $order ? $sort . " " . $order : "article_id desc";
			$res = \app\subschool\service\ZhArticlesService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			foreach ($res["rows"] as &$v) {
				$order = \think\facade\Db::name("article_pay")->where("a_id", $v["article_id"])->where("type", 1)->where("status", 1)->find();
				$v["price"] = $order["price"];
				$v["pay_status"] = $order["status"];
			}
			return json($res);
		}
	}
	public function delete()
	{
		$idx = $this->request->post("article_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhArticles::destroy(["article_id" => explode(",", $idx)]);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function hsz()
	{
		if (!$this->request->isAjax()) {
			return view("hsz");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["s_id"] = session("subschool.s_id");
			$where["wxapp_id"] = session("subschool.wxapp_id");
			$where["class_id"] = $this->request->param("class_id", "", "serach_in");
			$where["media_type"] = $this->request->param("media_type", "", "serach_in");
			$where["status"] = $this->request->param("status", "", "serach_in");
			$createtime_start = $this->request->param("createtime_start", "", "serach_in");
			$createtime_end = $this->request->param("createtime_end", "", "serach_in");
			$where["createtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "article_id,class_id,u_id,content,media_type,status,createtime,image";
			$orderby = $sort && $order ? $sort . " " . $order : "article_id desc";
			$res = \app\subschool\service\ZhArticlesService::hszList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function resumeData()
	{
		$idx = $this->request->post("article_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			$data = \app\subschool\model\ZhArticles::onlyTrashed()->where(["article_id" => explode(",", $idx)])->select();
			foreach ($data as $v) {
				$v->restore();
			}
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function trashDelete()
	{
		$idx = $this->request->post("article_id", "", "serach_in");
		if (!$idx) {
			$this->error("参数错误");
		}
		try {
			\app\subschool\model\ZhArticles::destroy(["article_id" => explode(",", $idx)], true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return json(["status" => "00", "msg" => "操作成功"]);
	}
	public function view()
	{
		$article_id = $this->request->get("article_id", "", "serach_in");
		if (!$article_id) {
			$this->error("参数错误");
		}
		$sql = "select a.*,b.class_name from gc_zh_articles as a left join gc_zh_forum_class as b on a.class_id = b.class_id where a.article_id = " . $article_id . " limit 1";
		$info = \think\facade\Db::connect("mysql")->query($sql);
		$info[0]["image"] = explode(",", $info[0]["image"]);
		$info[0]["user"] = \think\facade\Db::name("wechat_user")->find($info[0]["u_id"]);
		$this->view->assign("info", current($info));
		return view("view");
	}
	public function update()
	{
		if (!$this->request->isPost()) {
			$article_id = $this->request->get("article_id", "", "serach_in");
			if (!$article_id) {
				$this->error("参数错误");
			}
			$a = checkData(\app\subschool\model\ZhArticles::find($article_id));
			$a["image"] = explode(",", $a["image"]);
			$this->view->assign("info", $a);
			return view("update");
		} else {
			$postField = "article_id,class_id,content,media_type,status,createtime";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$a = checkData(\app\subschool\model\ZhArticles::find($data["article_id"]));
			if ($data["status"] == 2 && $a["paytime"]) {
				$order = \think\facade\Db::name("article_pay")->where("a_id", $a["article_id"])->find();
				try {
					\app\api\service\PaymentService::instance($a["wxapp_id"])->refund($order["ordersn"], "T" . date("YmdHis") . rand(1000, 9999), $order["price"], $order["price"], $order["pay_type"]);
					$res = \app\subschool\service\ZhArticlesService::update($data);
					return json(["status" => "00", "msg" => "修改成功"]);
				} catch (\Exception $e) {
					return json(["status" => "01", "msg" => $e->getMessage()]);
				}
			} else {
				if ($data["status"] == 1) {
					$order = \think\facade\Db::name("article_pay")->where("a_id", $a["article_id"])->find();
					if ($a["deadtime"]) {
						\app\api\controller\Distribution::index(5, $order["id"]);
					}
				}
				$res = \app\subschool\service\ZhArticlesService::update($data);
				return json(["status" => "00", "msg" => "修改成功"]);
			}
		}
	}
}