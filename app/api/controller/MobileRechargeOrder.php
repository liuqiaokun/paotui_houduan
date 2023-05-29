<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class MobileRechargeOrder extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 200, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["wxapp_id"] = $this->request->post("wxapp_id");
		$openid = $this->request->post("openid");
		$user = \think\facade\Db::name("wechat_user")->where(["wxapp_id" => $where["wxapp_id"], "openid" => $openid])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "用户不存在", "");
		}
		$where["u_id"] = $user["u_id"];
		$field = "*";
		$orderby = "oid desc";
		$res = \app\api\service\MobileRechargeOrderService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		foreach ($res["list"] as &$v) {
			$v["createtime"] = date("Y/m/d H:i", $v["createtime"]);
		}
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$postField = "wxapp_id,s_id,ordersn,u_id,price,price_val,operator,mobile,status,createtime,openid";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$setting = \think\facade\Db::name("setting")->where("wxapp_id", $data["wxapp_id"])->find();
		$user = \think\facade\Db::name("wechat_user")->where(["wxapp_id" => $data["wxapp_id"], "openid" => $data["openid"]])->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "用户不存在", "");
		}
		$data["pay_type"] = $setting["pay_type"];
		$data["ordersn"] = "HFCZ" . time() . rand(10, 99) . rand(10, 99);
		$data["u_id"] = $user["u_id"];
		$res = \app\api\service\MobileRechargeOrderService::add($data);
		$user = \think\facade\Db::name("wechat_user")->find($data["u_id"]);
		$notify_url = "https://" . $_SERVER["HTTP_HOST"] . "/api/MobileRechargeOrder/notify";
		$js_pay = \app\api\service\PaymentService::instance($data["wxapp_id"])->create($user["openid"], $data["ordersn"], $data["price"], "话费充值", $setting["pay_type"], ["notify_url" => $notify_url]);
		return $this->ajaxReturn($this->successCode, "下单成功", $js_pay);
	}
	public function notify()
	{
		$pay_type = input("pay_type");
		$postStr = file_get_contents("php://input");
		if ($pay_type == 1) {
			$getData = json_decode($postStr, true);
			if ($getData["tradeStatus"] = "SUCCESS") {
				$ordersn = $getData["outTradeNo"];
				$price = $getData["payAmt"];
			} else {
				return false;
			}
		} else {
			$getData = $this->xmlstr_to_array($postStr);
			if ($getData["return_code"] === "SUCCESS" && $getData["result_code"] === "SUCCESS") {
				$ordersn = $getData["out_trade_no"];
				$price = $getData["total_fee"];
			} else {
				return false;
			}
		}
		$order = \think\facade\Db::name("mobile_recharge_order")->where("ordersn", $ordersn)->find();
		file_put_contents("huidiao1.txt", " 订单号参数11 " . json_encode($getData) . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		file_put_contents("huidiao1.txt", " 订单号参数11 " . json_encode($order) . " 时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
		if ($price == $order["price"] * 100 && $order["status"] == 0) {
			file_put_contents("huidiao1.txt", " 执行这里  时间 " . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
			\think\facade\Db::name("mobile_recharge_order")->where("ordersn", $ordersn)->update(["status" => 1, "paytime" => time()]);
		}
		$str = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
		echo $str;
	}
	public function xmlstr_to_array($xmlstr)
	{
		libxml_disable_entity_loader(true);
		$xmlstring = simplexml_load_string($xmlstr, "SimpleXMLElement", LIBXML_NOCDATA);
		$val = json_decode(json_encode($xmlstring), true);
		return $val;
	}
}