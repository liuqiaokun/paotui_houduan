<?php

//decode by http://www.yunlu99.com/
namespace app\api\controller;

class Dmhmarketcategory extends Common
{
	public function index()
	{
		if (!$this->request->isPost()) {
			throw new \think\exception\ValidateException("请求错误");
		}
		$limit = $this->request->post("limit", 20, "intval");
		$page = $this->request->post("page", 1, "intval");
		$where = [];
		$where["s_id"] = $this->request->post("s_id", "", "serach_in");
		$where["wxapp_id"] = $this->request->post("wxapp_id", "", "serach_in");
		$field = "*";
		$orderby = "sort desc";
		$res = \app\api\service\DmhmarketcategoryService::indexList(formatWhere($where), $field, $orderby, $limit, $page);
		return $this->ajaxReturn($this->successCode, "返回成功", htmlOutList($res));
	}
}