<?php

//decode by http://www.yunlu99.com/
namespace app\ApplicationName\controller;

class Form extends Base
{
	public function index()
	{
		if ($this->request->isPost()) {
			$formData = $this->request->post();
			if (empty($formData["form_id"])) {
				$this->error("模型ID不能为空");
			}
			try {
				$res = \app\ApplicationName\service\FormExtendService::saveData($formData);
			} catch (\Exception $e) {
				$this->error($e->getMessage());
			}
			$this->success("提交成功");
		}
	}
}