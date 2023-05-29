<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Dmhinterests extends Admin
{
	public function index()
	{
		if (!$this->request->isAjax()) {
			$where["wxapp_id"] = session("accounts.wxapp_id");
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$where["order_sn"] = $this->request->param("order_sn", "", "serach_in");
			$where["s_id"] = $this->request->param("s_id", "", "serach_in");
			$createtime_start = $this->request->param("addtime_start", "", "serach_in");
			$createtime_end = $this->request->param("addtime_end", "", "serach_in");
			$where["addtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
			$field = "*";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\accounts\service\DmhinterestsService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			$datas = [];
			foreach ($res["rows"] as $K => $v) {
				$datas[$K] = $v;
				$user = $this->app->db->name("wechat_user")->where("u_id", $v["uid"])->find();
				$school = $this->app->db->name("school")->where("s_id", $v["s_id"])->find();
				$datas[$K]["user"] = $user["nickname"];
				$datas[$K]["school"] = $school["s_name"];
				$datas[$K]["addtime"] = date("Y-m-d H:i:s", $v["addtime"]);
			}
			$res["rows"] = $datas;
			return json($res);
		}
	}
	public function dumpData()
	{
		$where = [];
		$where["wxapp_id"] = session("accounts.wxapp_id");
		$where["order_sn"] = $this->request->param("order_sn", "", "serach_in");
		$where["s_id"] = $this->request->param("s_id", "", "serach_in");
		$createtime_start = $this->request->param("addtime_start", "", "serach_in");
		$createtime_end = $this->request->param("addtime_end", "", "serach_in");
		$where["addtime"] = ["between", [strtotime($createtime_start), strtotime($createtime_end)]];
		try {
			$fieldInfo = [];
			for ($j = 0; $j < 100; $j++) {
				$fieldInfo[] = $this->request->param($j);
			}
			$list = \app\accounts\model\Dmhinterests::where(formatWhere($where))->order("id desc")->select();
			$datas = [];
			foreach ($list as $K => $v) {
				$datas[$K] = $v;
				$user = $this->app->db->name("wechat_user")->where("u_id", $v["uid"])->find();
				$school = $this->app->db->name("school")->where("s_id", $v["s_id"])->find();
				$datas[$K]["user"] = $user["nickname"];
				$datas[$K]["school"] = $school["s_name"];
				$datas[$K]["addtime"] = date("Y-m-d H:i:s", $v["addtime"]);
			}
			$list = $datas;
			if (empty($list)) {
				throw new Exception("没有数据");
			}
			\app\accounts\service\DmhinterestsService::dumpData(htmlOutList($list), filterEmptyArray(array_unique($fieldInfo)));
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
	}
}