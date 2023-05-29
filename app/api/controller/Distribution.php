<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Distribution extends Common
{
	public function index($type, $oid)
	{
		file_put_contents("静态方法分销.txt", "*****789456");
		$table = $type == 2 ? "dmh_school_order" : ($type == 3 ? "dmh_market_order" : "article_pay");
		$remark = $type == 2 ? "下级发布跑腿订单" : ($type == 3 ? "下级购买二手商品" : ($type == 4 ? "下级打赏文章" : "下级置顶文章"));
		$order = \think\facade\Db::name($table)->find($oid);
		$uid = $type == 2 ? $order["u_id"] : ($type == 3 ? $order["purchase"] : $order["uid"]);
		$price = $type == 2 ? $order["s_money"] : ($type == 3 ? $order["commission"] : $order["rate"]);
		$school = \think\facade\Db::name("school")->find($order["s_id"]);
		$school["project"] = explode(",", $school["project"]);
		if ($school["is_distribution"] == 1) {
			if (in_array($type, $school["project"]) || in_array(1, $school["project"])) {
				$user = \think\facade\Db::name("wechat_user")->find($uid);
				if ($user["spid"] > 0) {
					$parent = \think\facade\Db::name("wechat_user")->find($user["spid"]);
					\think\facade\Db::startTrans();
					try {
						$add_money = $school["level1"] * $price / 100;
						$add_money = $add_money < 0 ? 0 : $add_money;
						$res1 = true;
						$res2 = true;
						$res3 = true;
						$res4 = true;
						$count = \think\facade\Db::name("spread_account_log")->where(["type" => $type, "o_id" => $oid])->count();
						if ($add_money > 0 && $count <= 0) {
							$res1 = \app\api\model\WechatUser::where("u_id", $parent["u_id"])->inc("spread_balance", $add_money)->update();
							$log = ["wxapp_id" => $order["wxapp_id"], "s_id" => $order["s_id"], "u_id" => $parent["u_id"], "from_id" => $uid, "price" => $add_money, "o_id" => $oid, "type" => $type, "operate" => 1, "remark" => $remark . "，订单编号" . $oid];
							$res2 = \think\facade\Db::name("spread_account_log")->insert($log);
						}
						if ($parent["spid"] > 0 && $count <= 0) {
							$pparent = \think\facade\Db::name("wechat_user")->find($parent["spid"]);
							$add_money1 = $school["level2"] * $price / 100;
							$add_money1 = $add_money1 < 0 ? 0 : $add_money1;
							if ($add_money1 > 0) {
								$res3 = \app\api\model\WechatUser::where("u_id", $pparent["u_id"])->inc("spread_balance", $add_money1)->update();
								$log = ["wxapp_id" => $order["wxapp_id"], "s_id" => $order["s_id"], "u_id" => $pparent["u_id"], "from_id" => $parent["u_id"], "price" => $add_money1, "o_id" => $oid, "type" => $type, "operate" => 1, "remark" => $remark . "，订单编号" . $oid];
								$res4 = \think\facade\Db::name("spread_account_log")->insert($log);
							}
						}
						if ($res1 && $res2 && $res3 && $res4) {
							\think\facade\Db::commit();
						} else {
							\think\facade\Db::rollback();
						}
					} catch (\Exception $e) {
					}
				}
			}
		}
	}
	public function accountLog()
	{
		$u_id = $this->request->uid;
		$type = $this->request->post("type");
		$operate = $this->request->post("operate");
		$page = $this->request->post("page", 1);
		$limit = $this->request->post("limit", 20);
		$type_array = ["跑腿" => 1, "二手" => 2, "打赏" => 3, "置顶" => 4];
		if ($operate) {
			$where["operate"] = $operate - 1;
		}
		$where["u_id"] = $u_id;
		$res = \think\facade\Db::name("spread_account_log")->where($where)->page($page, $limit)->order("id", "desc")->select();
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function myteam()
	{
		$u_id = $this->request->uid;
		$page = $this->request->post("page", 1);
		$limit = $this->request->post("limit", 20);
		$wxapp_id = $this->request->post("wxapp_id");
		$res = \think\facade\Db::name("wechat_user")->where(["spid" => $u_id, "wxapp_id" => $wxapp_id])->page($page, $limit)->order("u_id", "desc")->select()->toArray();
		foreach ($res as &$v) {
			$v["money"] = \think\facade\Db::name("spread_account_log")->where(["u_id" => $u_id, "from_id" => $v["u_id"]])->sum("price");
			$v["child"] = \think\facade\Db::name("wechat_user")->where(["spid" => $v["u_id"], "wxapp_id" => $wxapp_id])->count();
		}
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function childteam()
	{
		$u_id = $this->request->post("u_id");
		$page = $this->request->post("page", 1);
		$limit = $this->request->post("limit", 20);
		$wxapp_id = $this->request->post("wxapp_id");
		$res = \think\facade\Db::name("wechat_user")->where(["spid" => $u_id, "wxapp_id" => $wxapp_id])->page($page, $limit)->order("u_id", "desc")->select()->toArray();
		foreach ($res as &$v) {
			$v["money"] = \think\facade\Db::name("spread_account_log")->where(["u_id" => $u_id, "from_id" => $v["u_id"]])->sum("price");
		}
		$data["count"] = \think\facade\Db::name("wechat_user")->where(["spid" => $u_id, "wxapp_id" => $wxapp_id])->count();
		$data["userinfo"] = \think\facade\Db::name("wechat_user")->find($u_id);
		$data["userinfo"]["create_time"] = date("Y-m-d H:i:s", $data["userinfo"]["create_time"]);
		$data["list"] = $res;
		return $this->ajaxReturn($this->successCode, "操作成功", $data);
	}
	public function incomeStatistics()
	{
		$u_id = $this->request->uid;
		$wxapp_id = $this->request->post("wxapp_id");
		$today = [date("Y-m-d") . " 00:00:00", date("Y-m-d") . " 23:59:59"];
		$yesterday = [date("Y-m-d", strtotime("-1day")) . " 00:00:00", date("Y-m-d", strtotime("-1day")) . " 23:59:59"];
		$month = [date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y"))), date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("t"), date("Y")))];
		$year = [date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, date("Y"))), date("Y-m-d H:i:s", mktime(23, 59, 59, 12, 31, date("Y")))];
		$logwhere["u_id"] = $u_id;
		$logwhere["operate"] = 1;
		$logwhere["wxapp_id"] = $wxapp_id;
		$data["today_money"] = \think\facade\Db::name("spread_account_log")->where($logwhere)->where("createtime", "between", $today)->sum("price");
		$data["today_order"] = \think\facade\Db::name("spread_account_log")->where($logwhere)->where("createtime", "between", $today)->group("o_id")->count();
		$data["today_count"] = \think\facade\Db::name("wechat_user")->where(["spid" => $u_id])->where("spid_time", "between", $today)->count();
		$data["yesterday_money"] = \think\facade\Db::name("spread_account_log")->where($logwhere)->where("createtime", "between", $yesterday)->sum("price");
		$data["yesterday_order"] = \think\facade\Db::name("spread_account_log")->where($logwhere)->where("createtime", "between", $yesterday)->group("o_id")->count();
		$data["yesterday_count"] = \think\facade\Db::name("wechat_user")->where(["spid" => $u_id])->where("spid_time", "between", $yesterday)->count();
		$data["month_money"] = \think\facade\Db::name("spread_account_log")->where($logwhere)->where("createtime", "between", $month)->sum("price");
		$data["month_order"] = \think\facade\Db::name("spread_account_log")->where($logwhere)->where("createtime", "between", $month)->group("o_id")->count();
		$data["month_count"] = \think\facade\Db::name("wechat_user")->where(["spid" => $u_id])->where("spid_time", "between", $month)->count();
		$data["year_money"] = \think\facade\Db::name("spread_account_log")->where($logwhere)->where("createtime", "between", $year)->sum("price");
		$data["year_order"] = \think\facade\Db::name("spread_account_log")->where($logwhere)->where("createtime", "between", $year)->group("o_id")->count();
		$data["year_count"] = \think\facade\Db::name("wechat_user")->where(["spid" => $u_id])->where("spid_time", "between", $year)->count();
		return $this->ajaxReturn($this->successCode, "操作成功", $data);
	}
}