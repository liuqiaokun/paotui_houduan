<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\controller;

class Setting extends Admin
{
	public function initialize()
	{
		parent::initialize();
		if (in_array($this->request->action(), ["update"])) {
			$idx = $this->request->param("id", "", "serach_in");
			if ($idx) {
				foreach (explode(",", $idx) as $v) {
					$info = \app\accounts\model\Setting::find($v);
					if ($info["wxapp_id"] != session("accounts.wxapp_id")) {
						$this->error("你没有操作权限");
					}
				}
			}
		}
	}
	public function index()
	{
		if (!$this->request->isAjax()) {
			$count = \think\facade\Db::name("setting")->where("wxapp_id", session("accounts.wxapp_id"))->count();
			$this->view->assign("count", $count);
			return view("index");
		} else {
			$limit = $this->request->post("limit", 20, "intval");
			$offset = $this->request->post("offset", 0, "intval");
			$page = floor($offset / $limit) + 1;
			$where = [];
			$where["wxapp_id"] = session("accounts.wxapp_id");
			$order = $this->request->post("order", "", "serach_in");
			$sort = $this->request->post("sort", "", "serach_in");
			$field = "id,appid,appsecret,mch_id,mch_key";
			$orderby = $sort && $order ? $sort . " " . $order : "id desc";
			$res = \app\accounts\service\SettingService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
			return json($res);
		}
	}
	public function add()
	{
		if (!$this->request->isPost()) {
			$is_alipay = $this->is_alipay();
			$discount = $this->discount();
			$this->view->assign("is_alipay", $is_alipay);
			$this->view->assign("discount", $discount);
			return view("add");
		} else {
			$postField = "wxapp_id,apikey,pub_id,wxapppay,schoolpay,code,appid,is_alipay,alipayappid,publickey,privatekey,zfbpublickey,appsecret,mch_id,mch_key,template_new,template_grab,template_cancel,template_store,template_comment,template_pay,user_month_fee,user_year_fee,store_week_fee,store_month_fee,xcx_logo,back_logo,vip_content,privacy_content,help_content,toast_content,user_vip_switch,company_pay_switch,take_all_switch,second_check_switch,article_check_switch,index_quanzi_switch,index_toast_switch,index_rank_switch,index_module_switch,index_history_switch,is_anonymous_switch,runner_auth_switch,refund_cert,refund_key,app_name,qu_tips,ji_tips,shi_tips,wan_tips,withdraw_tips,home_adv_type,home_adv_id,second_adv_type,second_adv_id,step_price,start_fee,withdraw_min,is_address_show,is_address_must,is_attach_show,is_attach_must,is_servicetime_show,is_servicenum_show,mp_appid,mp_secret,is_open_reward,posting_instructions,mp_template_new,mp_template_grab,mp_template_cancel,mp_template_store,mp_template_pay,mp_code,mp_name,mp_logo,app_up_key,xcx_login_bg,index_articles_switch,is_open_pay,order_finish_time,index_grab_show,store_recharge_rule,index_store_show,index_rank_show,store_settle,explains,is_real,is_article_auth,dmgexplain,dmhimage,studentdmh,pay_type,merchant_no,p12_password,private_key,public_key,pay_method,serial_no,template_notice,schedule_template,bg_poster,template_cancel_rider,template_refuse,template_refuse_mp";
			$data = $this->request->only(explode(",", $postField), "post", null);
			$info = \think\facade\Db::name("setting")->where("wxapp_id", session("accounts.wxapp_id"))->find();
			if ($info) {
				return json(["status" => "01", "msg" => "添加失败"]);
			}
			if ($data["refund_cert"]) {
				$data["serial_no"] = openssl_x509_parse(file_get_contents($data["refund_cert"]))["serialNumberHex"];
			}
			$res = \app\accounts\service\SettingService::add($data);
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
			$is_alipay = $this->is_alipay();
			$discount = $this->discount();
			$this->view->assign("is_alipay", $is_alipay);
			$this->view->assign("discount", $discount);
			$this->view->assign("info", checkData(\app\accounts\model\Setting::find($id)));
			return view("update");
		} else {
			$postField = "id,wxapp_id,pub_id,wxapppay,schoolpay,apikey,code,appid,is_alipay,alipayappid,publickey,privatekey,zfbpublickey,appsecret,mch_id,mch_key,template_new,template_grab,template_cancel,template_store,template_comment,template_pay,user_month_fee,user_year_fee,store_week_fee,store_month_fee,xcx_logo,back_logo,vip_content,privacy_content,help_content,toast_content,user_vip_switch,company_pay_switch,take_all_switch,second_check_switch,article_check_switch,index_quanzi_switch,index_toast_switch,index_rank_switch,index_module_switch,index_history_switch,is_anonymous_switch,runner_auth_switch,refund_cert,refund_key,app_name,qu_tips,ji_tips,shi_tips,wan_tips,withdraw_tips,home_adv_type,home_adv_id,second_adv_type,second_adv_id,step_price,start_fee,withdraw_min,is_address_show,is_address_must,is_attach_show,is_attach_must,is_servicetime_show,is_servicenum_show,mp_appid,mp_secret,is_open_reward,posting_instructions,mp_template_new,mp_template_grab,mp_template_cancel,mp_template_store,mp_template_pay,mp_code,mp_name,mp_logo,app_up_key,xcx_login_bg,index_articles_switch,is_open_pay,order_finish_time,index_grab_show,store_recharge_rule,index_store_show,index_rank_show,store_settle,explains,is_real,is_article_auth,dmgexplain,dmhimage,studentdmh,pay_type,merchant_no,p12_password,private_key,public_key,pay_method,serial_no,template_notice,schedule_template,bg_poster,template_cancel_rider,template_refuse,template_refuse_mp";
			$data = $this->request->only(explode(",", $postField), "post", null);
			if ($data["refund_cert"]) {
				$data["serial_no"] = openssl_x509_parse(file_get_contents($data["refund_cert"]))["serialNumberHex"];
			}
			$res = \app\accounts\service\SettingService::update($data);
			return json(["status" => "00", "msg" => "修改成功"]);
		}
	}
	public function indexmodify()
	{
		if (!$this->request->isAjax()) {
			$count = \think\facade\Db::name("setting")->where("wxapp_id", session("accounts.wxapp_id"))->count();
			$this->view->assign("count", $count);
			return view("index");
		}
	}
	public function is_alipay()
	{
		$wxapp_id = session("accounts.wxapp_id");
//		$url1 = "http://send.fkynet.net/api/Plugins/is_purchase";
        $url1 = "";
		$res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=4&user_wxappid=" . $wxapp_id);
		$res = json_decode($res, true);
		if ($res["code"] == 200) {
			$data = 1;
		} else {
			$data = 0;
		}
		return $data;
	}
	public function discount()
	{
		$wxapp_id = session("accounts.wxapp_id");
//		$url1 = "http://send.fkynet.net/api/Plugins/is_purchase";
        $url1 = "";
        $res = file_get_contents($url1 . "?linkadr=" . $_SERVER["HTTP_HOST"] . "&wxapp_id=1&pid=5&user_wxappid=" . $wxapp_id);
		$res = json_decode($res, true);
		if ($res["code"] == 200) {
			$data = 1;
		} else {
			$data = 0;
		}
		return $data;
	}
}