<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class Synchronization extends Admin
{
	protected $wqConn;
	public function index()
	{
		if ($this->request->isAjax()) {
			$type = input("type", "", "trim");
			if ($type == 1) {
				$urladdress = input("urladdress", "", "trim");
				$username = input("username", "", "trim");
				$libraryname = input("libraryname", "", "trim");
				$librarypwd = input("librarypwd", "", "trim");
				$uniacid = input("uniacid", "", "trim");
				if (empty($urladdress) || empty($urladdress) || empty($username) || empty($libraryname) || empty($librarypwd) || empty($uniacid)) {
					return json(["code" => 0, "msg" => "参数不正确请填写参数"]);
				}
				$wqConn = $this->Configs($urladdress, $username, $libraryname, $librarypwd);
				$tabconfig = $this->tabconfig();
				$getall = $this->getall($tabconfig, $uniacid, $wqConn);
				if ($getall["code"] == 1) {
					return json(["code" => 1, "msg" => "同步成功"]);
				} else {
					return json(["code" => 0, "msg" => "同步失败请回复备份重新同步"]);
				}
			}
		}
		return view("index");
	}
	public function tabconfig()
	{
		$tab = ["gc_school_school", "gc_school_user", "gc_school_restaurant", "gc_school_store", "gc_school_goods_cate", "gc_school_goods"];
		return $tab;
	}
	public function datas($table, $v, $uniacid, $wqConn)
	{
		$data["list"] = [];
		$name = "test" . $uniacid;
		$account = db("account")->where("account", $name)->find();
		if (!$account) {
			$ins = ["account" => "test" . $uniacid, "pwd" => md5("123456" . config("my.password_secrect"))];
			$wxapp_id = db("account")->insertGetId($ins);
		} else {
			$wxapp_id = $account["wxapp_id"];
		}
		if ($v == "gc_school_school") {
			foreach ($table as $key => $vs) {
				$data["list"][$key]["s_name"] = $vs["s_name"];
				$data["list"][$key]["wxapp_id"] = $wxapp_id;
				$data["list"][$key]["plat_rate"] = $vs["rate"];
				$data["list"][$key]["school_rate"] = $vs["divide"];
				$data["list"][$key]["second_rate"] = $vs["second_rate"];
				$data["list"][$key]["edit_status"] = $vs["edit_status"];
				$data["list"][$key]["robot_key"] = $vs["robot_key"];
				$data["list"][$key]["step"] = $vs["step"];
				$data["list"][$key]["alipay_name"] = $vs["alipay_name"];
				$data["list"][$key]["alipay_account"] = $vs["account"];
			}
			$data["table"] = "school";
		}
		if ($v == "gc_school_user") {
			foreach ($table as $key => $vs) {
				if ($vs["status"] == 3) {
					$status = -1;
				} else {
					$status = $vs["status"];
				}
				if ($vs["t_sex"] == 1) {
					$sex = 2;
				} else {
					$sex = 0;
				}
				$wqs_id = $wqConn->name("gc_school_school")->where("s_id", $vs["s_id"])->find();
				$s_id = db("school")->where("s_name", $wqs_id["s_name"])->find();
				$data["list"][$key]["openid"] = $vs["openid"];
				$data["list"][$key]["nickname"] = $vs["nickname"];
				$data["list"][$key]["avatar"] = $vs["nickimg"];
				$data["list"][$key]["t_name"] = $vs["t_name"];
				$data["list"][$key]["balance"] = $vs["money"];
				$data["list"][$key]["phone"] = $vs["phone"];
				$data["list"][$key]["t_sex"] = $sex;
				$data["list"][$key]["run_status"] = $status;
				$data["list"][$key]["wxapp_id"] = $wxapp_id;
				$data["list"][$key]["s_id"] = $s_id["s_id"];
			}
			$data["table"] = "wechat_user";
		}
		if ($v == "gc_school_restaurant") {
			foreach ($table as $key => $vs) {
				$wqs_id = $wqConn->name("gc_school_school")->where("s_id", $vs["school_id"])->find();
				$s_id = db("school")->where("s_name", $wqs_id["s_name"])->find();
				$data["list"][$key]["wxapp_id"] = $wxapp_id;
				$data["list"][$key]["s_id"] = $s_id["s_id"];
				$data["list"][$key]["type_name"] = $vs["name"];
				$data["list"][$key]["type_image"] = $vs["img"];
			}
			$data["table"] = "zh_business_type";
		}
		if ($v == "gc_school_store") {
			foreach ($table as $key => $vs) {
				$wqtype_id = $wqConn->name("gc_school_restaurant")->where("id", $vs["rest_id"])->find();
				$type_id = db("zh_business_type")->where("type_name", $wqtype_id["name"])->find();
				$wquser = $wqConn->name("gc_school_user")->where("u_id", $vs["u_id"])->find();
				$user = db("wechat_user")->where("openid", $wquser["openid"])->find();
				$data["list"][$key]["wxadmin_name"] = $user["u_id"];
				$data["list"][$key]["wxapp_id"] = $wxapp_id;
				$data["list"][$key]["s_id"] = $type_id["s_id"];
				$data["list"][$key]["type_id"] = $type_id["type_id"];
				$data["list"][$key]["start_time"] = $vs["start_time"];
				$data["list"][$key]["end_time"] = $vs["end_time"];
				$data["list"][$key]["business_name"] = $vs["name"];
				$data["list"][$key]["business_address"] = $vs["address"];
				$data["list"][$key]["phone"] = $vs["phone"];
				$data["list"][$key]["expire_time"] = strtotime($vs["deadtime"]);
				$data["list"][$key]["business_image"] = $vs["img"];
				$data["list"][$key]["status"] = $vs["status"];
				$data["list"][$key]["type"] = $vs["type"];
			}
			$data["table"] = "zh_business";
		}
		if ($v == "gc_school_goods_cate") {
			foreach ($table as $key => $vs) {
				$wqs_id = $wqConn->name("gc_school_school")->where("s_id", $vs["school_id"])->find();
				$s_id = db("school")->where("s_name", $wqs_id["s_name"])->find();
				$wqbusiness_id = $wqConn->name("gc_school_store")->where("id", $vs["store_id"])->find();
				$business_id = db("zh_business")->where("business_name", $wqbusiness_id["name"])->find();
				$data["list"][$key]["wxapp_id"] = $wxapp_id;
				$data["list"][$key]["s_id"] = $s_id["s_id"];
				$data["list"][$key]["business_id"] = $business_id["business_id"];
				$data["list"][$key]["goods_type_name"] = $vs["name"];
			}
			$data["table"] = "zh_goods_type";
		}
		if ($v == "gc_school_goods") {
			foreach ($table as $key => $vs) {
				$wqs_id = $wqConn->name("gc_school_school")->where("s_id", $vs["school_id"])->find();
				$s_id = db("school")->where("s_name", $wqs_id["s_name"])->find();
				$wqbusiness_id = $wqConn->name("gc_school_store")->where("id", $vs["store_id"])->find();
				$business_id = db("zh_business")->where("business_name", $wqbusiness_id["name"])->find();
				$wqgoods_type_id = $wqConn->name("gc_school_goods_cate")->where("id", $vs["cate_id"])->find();
				$goods_type_id = db("zh_goods_type")->where("goods_type_name", $wqgoods_type_id["name"])->find();
				$data["list"][$key]["wxapp_id"] = $wxapp_id;
				$data["list"][$key]["s_id"] = $s_id["s_id"];
				$data["list"][$key]["business_id"] = $business_id["business_id"];
				$data["list"][$key]["goods_type_id"] = $goods_type_id["goods_type_id"];
				$data["list"][$key]["goods_name"] = $vs["name"];
				$data["list"][$key]["price"] = $vs["price"];
				$data["list"][$key]["goods_img"] = $vs["img"];
				$data["list"][$key]["status"] = $vs["status"];
			}
			$data["table"] = "zh_goods";
		}
		return $data;
	}
	public function inserts($data)
	{
		$res = db($data["table"])->insertAll($data["list"]);
		if ($res) {
			return json(["code" => 1, "msg" => "插入成功"]);
		} else {
			return json(["code" => 0, "msg" => "同步失败"]);
		}
	}
	public function Configs($urladdress, $libraryname, $username, $librarypwd)
	{
		$dbConfig = ["type" => "mysql", "dsn" => "", "hostname" => $urladdress, "database" => $libraryname, "username" => $username, "password" => $librarypwd, "hostport" => "3306", "params" => [], "charset" => "utf8", "prefix" => "ims_"];
		$DbConfig = Config("database");
		$DbConfig["connections"]["wq"] = $dbConfig;
		\think\facade\Config::set($DbConfig, "database");
		$wqConn = \think\facade\Db::connect("wq");
		return $wqConn;
	}
	public function getall($table, $uniacid, $wqConn)
	{
		foreach ($table as $key => $v) {
			$this->tableData($v, $uniacid, $wqConn);
		}
		$array = ["code" => 1, "msg" => "同步成功"];
		return $array;
	}
	public function tableData($table, $uniacid, $wqConn, $page = 1)
	{
		$data = $wqConn->name($table)->where("uniacid", $uniacid)->page($page, 500)->select()->toArray();
		$tpdata = $this->datas($data, $table, $uniacid, $wqConn);
		$this->inserts($tpdata);
		if (!empty($data)) {
			$this->tableData($table, $uniacid, $wqConn, $page + 1);
		}
	}
}