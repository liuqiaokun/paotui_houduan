<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class UserWithdraw extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$where["u_id"] = $this->request->uid;
		$where["status"] = $this->request->post("status", "", "serach_in");
		$field = "*";
		$orderby = "id desc";
		$res = \app\api\service\UserWithdrawService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$postField = "wxapp_id,u_id,account,name,price,type,status,create_time,update_time";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["u_id"] = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->find($data["u_id"]);
		$is_store = $this->request->post("is_store");
		$is_spread = $this->request->post("is_spread");
		$balance = $is_store == 1 ? $user["store_balance"] : ($is_spread == 1 ? $user["spread_balance"] : $user["balance"]);
		if ($balance < $data["price"]) {
			return $this->ajaxReturn($this->errorCode, "余额不足", 0);
		}
		$data["is_store"] = $is_store;
		$data["is_spread"] = $is_spread ? $is_spread : 0;
		$data["out_detail_no"] = date("YmdHis") . rand(100, 1000);
		\think\facade\Db::startTrans();
		try {
			$res = \app\api\service\UserWithdrawService::add($data);
			if ($is_spread == 1) {
				$res1 = \think\facade\Db::name("spread_account_log")->insert(["wxapp_id" => $data["wxapp_id"], "s_id" => $this->request->post("s_id"), "u_id" => $data["u_id"], "price" => $data["price"], "operate" => 0, "remark" => "用户申请提现"]);
			} else {
				$remark = $is_store == 1 ? "商家申请提现" : "用户申请提现";
				$log["wxapp_id"] = $data["wxapp_id"];
				$log["uid"] = $data["u_id"];
				$log["price"] = $data["price"];
				$log["type"] = $is_store == 1 ? 3 : 1;
				$log["operate"] = -1;
				$log["remark"] = $remark;
				$res1 = \think\facade\Db::name("user_account_log")->insert($log);
			}
			$field = $is_store == 1 ? "store_balance" : ($is_spread == 1 ? "spread_balance" : "balance");
			$res2 = \think\facade\Db::name("wechat_user")->where("u_id", $data["u_id"])->dec($field, $data["price"])->update();
			if ($res && $res1 && $res2) {
				\think\facade\Db::commit();
				return $this->ajaxReturn($this->successCode, "提交成功", $res);
			} else {
				throw new \Exception("提交申请失败");
			}
		} catch (\Exception $e) {
			\think\facade\Db::rollback();
			return $this->ajaxReturn($this->errorCode, $e->getMessage(), 0);
		}
	}
}