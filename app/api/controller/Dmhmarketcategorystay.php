<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhmarketcategorystay extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["m_id"] = $this->request->post("m_id", "", "serach_in");
		$wxapp_id = $this->request->post("wxapp_id", "", "serach_in");
		$where["pid"] = 0;
		$field = "a.*,b.nickname,b.avatar";
		$orderby = "id desc";
		$res = \app\api\service\DmhmarketcategorystayService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		$res["list"] = formartList(["id", "pid", "", ""], $res["list"]);
		foreach ($res["list"] as &$value) {
			$userData = \think\facade\Db::name("wechat_user")->where(["u_id" => $value["u_id"], "wxapp_id" => $wxapp_id])->find();
			$value["nickname"] = $userData["nickname"];
			$value["avatar"] = $userData["avatar"];
			$time = new \utils\Time();
			$value["create_time"] = $time->timeDiff($value["create_time"]);
			$a = $this->commentRecursion($value["id"], $value["u_id"], "gc_dmh_market_category_stay");
			$value["child"] = $a;
			$value["block"] = false;
		}
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function commentRecursion($pid, $uid, $table)
	{
		$result = [];
		$data = \think\facade\Db::table($table)->alias("g")->leftJoin("wechat_user b", "b.u_id = g.u_id")->where("g.pid", $pid)->field("g.*,b.nickname,b.avatar")->select();
		$p_user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		foreach ($data as &$v) {
			$time = new \utils\Time();
			$v["create_time"] = $time->timeDiff($v["create_time"]);
			$result[] = $v;
			$result = array_merge($result, $this->commentRecursion($v["id"], $v["u_id"], $table));
		}
		return $result;
	}
	public function add()
	{
		$postField = "s_id,wxapp_id,m_id,u_id,details,pid,create_time";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$res = \app\api\service\DmhmarketcategorystayService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function delete()
	{
		$idx = $this->request->post("ids", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["id"] = explode(",", $idx);
		try {
			\app\api\model\Dmhmarketcategorystay::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
}