<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class ZhGoodsType extends Common
{
	public function index()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$limit = $this->request->get("limit", 20, "intval");
		$page = $this->request->get("page", 1, "intval");
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "wxadmin_name" => $uid])->value("business_id");
		$where = ["wxapp_id" => $wxapp_id, "business_id" => $businessId];
		$field = "*";
		$orderby = "goods_type_id desc";
		$res = \app\api\service\ZhGoodsTypeService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
	public function add()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "wxadmin_name" => $uid])->value("business_id");
		$postField = "goods_type_name";
		$data = $this->request->only(explode(",", $postField), "post", null);
		$data["s_id"] = $s_id;
		$data["wxapp_id"] = $wxapp_id;
		$data["business_id"] = $businessId;
		$data["createtime"] = time();
		$judge = $this->msg_check($data["goods_type_name"], $data["wxapp_id"]);
		if (!$judge) {
			return $this->ajaxReturn($this->errorCode, "内容含有违法违规内容");
		}
		$res = \app\api\service\ZhGoodsTypeService::add($data);
		return $this->ajaxReturn($this->successCode, "操作成功", $res);
	}
	public function update()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "wxadmin_name" => $uid])->value("business_id");
		$postField = "goods_type_id,goods_type_name";
		$data = $this->request->only(explode(",", $postField), "post", null);
		if (empty($data["goods_type_id"])) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$where["goods_type_id"] = $data["goods_type_id"];
		$where["wxapp_id"] = $wxapp_id;
		$where["s_id"] = $s_id;
		$where["business_id"] = $businessId;
		$res = \app\api\service\ZhGoodsTypeService::update($where, $data);
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function delete()
	{
		$wxapp_id = $this->request->post("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->post("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "s_id" => $s_id, "wxadmin_name" => $uid])->value("business_id");
		$idx = $this->request->post("goods_type_id", "", "serach_in");
		if (empty($idx)) {
			throw new \think\exception\ValidateException("参数错误");
		}
		$data["goods_type_id"] = explode(",", $idx);
		$data["wxapp_id"] = $wxapp_id;
		$data["s_id"] = $s_id;
		$data["business_id"] = $businessId;
		try {
			\app\api\model\ZhGoodsType::destroy($data, true);
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return $this->ajaxReturn($this->successCode, "操作成功");
	}
	public function view()
	{
		$wxapp_id = $this->request->get("wxapp_id");
		if (!$wxapp_id) {
			return $this->ajaxReturn($this->errorCode, "缺少平台参数");
		}
		$wxSetting = \think\facade\Db::name("setting")->where("wxapp_id", $wxapp_id)->find();
		if (!$wxSetting) {
			return $this->ajaxReturn($this->errorCode, "平台参数未配置");
		}
		$s_id = $this->request->get("s_id");
		if (!$s_id) {
			return $this->ajaxReturn($this->errorCode, "缺少学校参数");
		}
		$sSetting = \think\facade\Db::name("school")->where("s_id", $s_id)->find();
		if (!$sSetting) {
			return $this->ajaxReturn($this->errorCode, "学校不存在");
		}
		$uid = $this->request->uid;
		$user = \think\facade\Db::name("wechat_user")->where("u_id", $uid)->find();
		if (!$user) {
			return $this->ajaxReturn($this->errorCode, "未知用户");
		}
		$businessId = \think\facade\Db::name("zh_business")->where(["wxapp_id" => $wxapp_id, "wxadmin_name" => $uid])->value("business_id");
		$data["goods_type_id"] = $this->request->get("goods_type_id");
		$data["wxapp_id"] = $wxapp_id;
		$data["business_id"] = $businessId;
		$field = "goods_type_id,business_id,goods_type_name";
		$res = checkData(\app\api\model\ZhGoodsType::field($field)->where($data)->find());
		return $this->ajaxReturn($this->successCode, "返回成功", $res);
	}
}