<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\controller\Sys;

class Build extends \app\gcadmin\controller\Admin
{
	public function create()
	{
		$menu_id = $this->request->post("menu_id", "", "intval");
		$actionList = model\Action::where("menu_id", $menu_id)->order("sortid asc")->select()->toArray();
		$menuInfo = model\Menu::find($menu_id);
		$applicationInfo = model\Application::find($menuInfo["app_id"]);
		if ($applicationInfo["app_type"] == 1) {
			if ($menuInfo["menu_id"] == config("my.config_module_id")) {
				self::createConfig($applicationInfo, $menuInfo);
				return json(["status" => "00", "msg" => "生成成功"]);
			} else {
				if (!$menuInfo || !$applicationInfo || empty($menuInfo["controller_name"])) {
					return json(["status" => "01", "msg" => "菜单信息错误"]);
				}
				if ($menuInfo["is_create"] && $this->createAdminModule($menuInfo, $applicationInfo, $actionList)) {
					return json(["status" => "00", "msg" => "生成成功"]);
				}
			}
		} else {
			if ($menuInfo["is_create"] && $this->createApiModule($menuInfo, $applicationInfo, $actionList)) {
				return json(["status" => "00", "msg" => "生成成功"]);
			}
		}
	}
	public function createAdminModule($menuInfo, $applicationInfo, $actionList)
	{
		$menu_id = $menuInfo["menu_id"];
		$pk_id = $menuInfo["pk_id"];
		$str .= "<?php \n";
		!is_null(config("my.comment.file_comment")) ? config("my.comment.file_comment") : true;
		if (config("my.comment.file_comment")) {
			$str .= "/*\n";
			$str .= " module:\t\t" . $menuInfo["title"] . "\n";
			$str .= " create_time:\t" . date("Y-m-d H:i:s") . "\n";
			$str .= " author:\t\t" . config("my.comment.author") . "\n";
			$str .= " contact:\t\t" . config("my.comment.contact") . "\n";
			$str .= "*/\n\n";
		}
		$str .= "namespace app\\" . $applicationInfo["app_dir"] . "\\controller" . getDbName($menuInfo["controller_name"]) . ";\n\n";
		$str .= "use app\\" . $applicationInfo["app_dir"] . "\\service\\" . getUseName($menuInfo["controller_name"]) . "Service;\n";
		$str .= "use app\\" . $applicationInfo["app_dir"] . "\\model\\" . getUseName($menuInfo["controller_name"]) . " as " . getControllerName($menuInfo["controller_name"]) . "Model;\n";
		if (strpos($menuInfo["controller_name"], "/") > 0) {
			$str .= "use app\\" . $applicationInfo["app_dir"] . "\\controller\\Admin;\n";
		}
		$str .= "use think\\facade\\Db;\n";
		$str .= "\n";
		$str .= "class " . getControllerName($menuInfo["controller_name"]) . " extends Admin {\n\n\n";
		$fieldList = model\Field::where(["menu_id" => $menu_id])->order("sortid asc")->select();
		foreach ($fieldList as $k => $v) {
			if ($v["type"] == 15 && $v["search_show"] == 1) {
				$session_field = $v["field"];
			}
			if (in_array($v["type"], [22, 23])) {
				$updateExt_field .= "," . $v["field"];
			}
			if ($v["type"] == 14) {
				$hiden_fileld = true;
			}
			$postFields[] = $v["field"];
		}
		$addInfo = model\Action::where(["type" => 3, "menu_id" => $menu_id])->find();
		foreach ($actionList as $m => $n) {
			if (in_array($n["type"], [4, 5, 6, 16, 7, 8, 9, 15])) {
				$action_auth .= "'" . $n["action_name"] . "',";
			}
		}
		if ($session_field && in_array($session_field, explode(",", $addInfo["fields"])) && $action_auth) {
			$str .= "\tfunction initialize(){\n";
			$str .= "\t\tparent::initialize();\n";
			$str .= "\t\tif(in_array(\$this->request->action(),[" . rtrim($action_auth, ",") . "])){\n";
			$str .= "\t\t\t\$idx = \$this->request->param('" . $pk_id . "','','serach_in');\n";
			$str .= "\t\t\tif(\$idx){\n";
			$str .= "\t\t\t\tforeach(explode(',',\$idx) as \$v){\n";
			$str .= "\t\t\t\t\t\$info = " . getControllerName($menuInfo["controller_name"]) . "Model::find(\$v);\n";
			if ($applicationInfo["app_id"] == 1) {
				$str .= "\t\t\t\t\tif(session('" . $applicationInfo["app_dir"] . ".role_id') <> 1 && \$info['" . $session_field . "'] <> session('" . $applicationInfo["app_dir"] . "." . $session_field . "')) \$this->error('你没有操作权限');\n";
			} else {
				$str .= "\t\t\t\t\tif(\$info['" . $session_field . "'] <> session('" . $applicationInfo["app_dir"] . "." . $session_field . "')) \$this->error('你没有操作权限');\n";
			}
			$str .= "\t\t\t\t}\n";
			$str .= "\t\t\t}\n";
			$str .= "\t\t}\n";
			$str .= "\t}\n\n";
		}
		foreach ($actionList as $key => $val) {
			switch ($val["type"]) {
				case in_array($val["type"], [1, 32]):
					$fieldList = model\Field::where(["menu_id" => $menu_id])->order("sortid asc")->select();
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\tif (!\$this->request->isAjax()){\n";
						$str .= "\t\t\treturn view('" . $val["action_name"] . "');\n";
						$str .= "\t\t}else{\n";
						$str .= "\t\t\t\$limit  = \$this->request->post('limit', 20, 'intval');\n";
						$str .= "\t\t\t\$offset = \$this->request->post('offset', 0, 'intval');\n";
						$str .= "\t\t\t\$page   = floor(\$offset / \$limit) +1 ;\n\n";
						if ($fieldList) {
							$pre = "";
							$str .= "\t\t\t\$where = [];\n";
							if ($val["relate_table"] && $val["relate_field"] || strpos(strtolower($val["sql_query"]), "join") > 0) {
								$pre = "a.";
								$softDeleteAction = db("action")->where(["menu_id" => $menuInfo["menu_id"], "type" => 31])->value("action_name");
								if ($softDeleteAction) {
									if ($val["type"] == 1) {
										$str .= "\t\t\t\$where['" . $pre . "delete_time'] = ['exp','is null'];\n";
									}
									if ($val["type"] == 32) {
										$str .= "\t\t\t\$where['" . $pre . "delete_time'] = ['exp','is not null'];\n";
									}
								}
							}
							foreach ($fieldList as $k => $v) {
								if ($v["search_show"] == 1 && in_array($v["type"], [1, 2, 3, 4, 6, 7, 12, 13, 15, 17, 20, 21, 23, 27, 28, 29, 30]) || $v["type"] == 14) {
									if ($v["type"] == 4) {
										$str .= "\n";
										$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['find in set',\$this->request->param('" . $v["field"] . "', '', 'serach_in')];\n";
									} elseif ($v["type"] == 7) {
										$str .= "\n";
										$str .= "\t\t\t\$" . $v["field"] . "_start = \$this->request->param('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\t\$" . $v["field"] . "_end = \$this->request->param('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[strtotime(\$" . $v["field"] . "_start),strtotime(\$" . $v["field"] . "_end)]];\n";
									} elseif ($v["type"] == 12) {
										$str .= "\n";
										$str .= "\t\t\t\$" . $v["field"] . "_start = \$this->request->param('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\t\$" . $v["field"] . "_end = \$this->request->param('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[strtotime(\$" . $v["field"] . "_start),strtotime(\$" . $v["field"] . "_end)]];\n";
									} elseif ($v["type"] == 13) {
										$str .= "\n";
										$str .= "\t\t\t\$" . $v["field"] . "_start = \$this->request->param('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\t\$" . $v["field"] . "_end = \$this->request->param('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[\$" . $v["field"] . "_start,\$" . $v["field"] . "_end]];\n";
									} elseif ($v["type"] == 15) {
										if ($applicationInfo["app_id"] == 1) {
											$str .= "\t\t\tif(session('" . $applicationInfo["app_dir"] . ".role_id') <> 1){\n";
											$str .= "\t\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = session('" . $applicationInfo["app_dir"] . "." . $v["field"] . "');\n";
											$str .= "\t\t\t}\n";
										} else {
											$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = session('" . $applicationInfo["app_dir"] . "." . $v["field"] . "');\n";
										}
									} elseif ($v["type"] == 17) {
										foreach (explode("|", $v["field"]) as $m => $n) {
											$str .= "\t\t\t\$where['" . $pre . "" . $n . "'] = \$this->request->param('" . $n . "', '', 'serach_in');\n";
										}
									} elseif ($v["type"] == 27) {
										$str .= "\n";
										$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['find in set',\$this->request->param('" . $v["field"] . "', '', 'serach_in')];\n";
									} else {
										if ($v["field"] == "name") {
											$search_field = "name_s";
										} else {
											$search_field = $v["field"];
										}
										if ($v["search_type"]) {
											$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['like',\$this->request->param('" . $search_field . "', '', 'serach_in')];\n";
										} else {
											$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = \$this->request->param('" . $search_field . "', '', 'serach_in');\n";
										}
									}
								}
								$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
								if (in_array($v["list_show"], [1, 2]) && service\BuildService::getFieldStatus($v["field"], $menuInfo["table_name"], $connect)) {
									$list_fields .= str_replace("|", ",", $v["field"]) . ",";
								}
							}
						}
						if (!empty($val["tree_config"])) {
							$list_fields = "*";
						}
						$str .= "\n";
						$str .= "\t\t\t\$order  = \$this->request->post('order', '', 'serach_in');\t//排序字段 bootstrap-table 传入\n";
						$str .= "\t\t\t\$sort  = \$this->request->post('sort', '', 'serach_in');\t\t//排序方式 desc 或 asc\n";
						$str .= "\n";
						if (!empty($val["sql_query"])) {
							$list_fields = "";
						} else {
							if (!empty($val["relate_table"]) && !empty($val["relate_field"]) && !empty($val["fields"])) {
								$list_fields = $val["list_field"];
							} else {
								$list_fields = rtrim($list_fields, ",");
							}
						}
						$str .= "\t\t\t\$field = '" . $list_fields . "';\n";
						$orderByField = $pk_id;
						$sortidField = db("field")->where(["menu_id" => $menuInfo["menu_id"], "type" => 22])->value("field");
						if ($sortidField) {
							$orderByField = $sortidField . " desc," . $pk_id;
						}
						$orderby = !empty($val["default_orderby"]) ? $val["default_orderby"] : $orderByField . " desc";
						$str .= "\t\t\t\$orderby = (\$sort && \$order) ? \$sort.' '.\$order : '" . $orderby . "';\n\n";
						if (!empty($val["sql_query"])) {
							if (!strpos($val["sql_query"], ".")) {
								$val["sql_query"] = str_replace("'", "\"", $val["sql_query"]);
							}
							$str .= "\t\t\t\$sql = '" . str_replace(["\r\n", "\r", "\n"], " ", $val["sql_query"]) . "';\n";
							$str .= "\t\t\t\$limit = (\$page-1) * \$limit.','.\$limit;\n";
							if ($menuInfo["connect"]) {
								$str .= "\t\t\t\$res = \\xhadmin\\CommonService::loadList(\$sql,formatWhere(\$where),\$limit,\$orderby,'" . $menuInfo["connect"] . "');\n";
							} else {
								$str .= "\t\t\t\$res = \\xhadmin\\CommonService::loadList(\$sql,formatWhere(\$where),\$limit,\$orderby);\n";
							}
						} else {
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "List(formatWhere(\$where),\$field,\$orderby,\$limit,\$page);\n";
						}
						if (!empty($val["tree_config"])) {
							$tree_config = explode(",", $val["tree_config"]);
							$str .= "\t\t\t\$res['rows'] = formartList(['" . $pk_id . "', '" . $tree_config[0] . "', '" . $tree_config[1] . "','" . $tree_config[1] . "'],\$res['rows']);\n";
						}
						$str .= "\t\t\treturn json(\$res);\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
						$list_fields = "";
						$field = "";
					}
					if ($val["is_view_create"] !== 0) {
						$hszActionList = $actionList;
						$norActionList = $actionList;
						if ($val["type"] == 32) {
							foreach ($fieldList as $m => $n) {
								if (in_array($n["type"], [22, 23])) {
									unset($fieldList[$m]);
								}
							}
							foreach ($hszActionList as $k => $v) {
								if (!in_array($v["type"], [33, 34])) {
									unset($hszActionList[$k]);
								}
							}
							self::createIndexTpl($applicationInfo, $menuInfo, $val, $fieldList, $hszActionList);
						} else {
							foreach ($norActionList as $k => $v) {
								if (in_array($v["type"], [33, 34])) {
									unset($norActionList[$k]);
								}
							}
							self::createIndexTpl($applicationInfo, $menuInfo, $val, $fieldList, $norActionList);
						}
					}
					break;
				case 3:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\tif (!\$this->request->isPost()){\n";
						$str .= "\t\t\treturn view('" . $val["action_name"] . "');\n";
						$str .= "\t\t}else{\n";
						$fieldList = model\Field::where(["menu_id" => $menu_id, "is_post" => 1])->order("sortid asc")->order("sortid asc")->select()->toArray();
						$relateFields = "";
						if ($val["relate_table"] && $val["relate_field"]) {
							$relateTableFieldList = service\BuildService::getRelateFieldList($val["relate_table"]);
							$fieldList = array_merge($fieldList, $relateTableFieldList);
							foreach ($relateTableFieldList as $k => $v) {
								if ($v["is_post"] == 1) {
									$relateFields .= $v["field"] . ",";
								}
							}
							$val["fields"] = $relateFields . $val["fields"];
						}
						$str .= "\t\t\t\$postField = '" . str_replace("|", ",", $val["fields"]) . "';\n";
						$str .= "\t\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$data);\n";
						$sortidField = db("field")->where(["menu_id" => $menuInfo["menu_id"], "type" => 22])->value("field");
						if ($sortidField) {
							$str .= "\t\t\tif(\$res && empty(\$data['" . $sortidField . "'])){\n";
							$str .= "\t\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::update(['" . $sortidField . "'=>\$res,'" . $pk_id . "'=>\$res]);\n";
							$str .= "\t\t\t}\n";
						}
						$str .= "\t\t\treturn json(['status'=>'00','msg'=>'添加成功']);\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
					}
					$relateFields = "";
					if ($val["is_view_create"] !== 0) {
						self::createInfoTpl($applicationInfo, $menuInfo, $val, service\BuildService::array_unset_tt($fieldList, "field"));
					}
					break;
				case 4:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\tif (!\$this->request->isPost()){\n";
						$str .= "\t\t\t\$" . $pk_id . " = \$this->request->get('" . $pk_id . "','','serach_in');\n";
						$str .= "\t\t\tif(!\$" . $pk_id . ") \$this->error('参数错误');\n";
						if (!empty($val["relate_table"]) && !empty($val["relate_field"])) {
							if (!$val["list_field"]) {
								$field = "a.*,b.*";
							} else {
								$field = $val["list_field"];
							}
							$str .= "\t\t\t\$info = db('" . $menuInfo["table_name"] . "')->field('" . $field . "')->alias('a')->join('" . $val["relate_table"] . " b','a." . $menuInfo["pk_id"] . "=b." . $pk_id . "','left')->where('a." . $pk_id . "',\$" . $pk_id . ")->find();\n";
							$str .= "\t\t\t\$this->view->assign('info',checkData(\$info));\n";
						} else {
							$str .= "\t\t\t\$this->view->assign('info',checkData(" . getControllerName($menuInfo["controller_name"]) . "Model::find(\$" . $pk_id . ")));\n";
						}
						$str .= "\t\t\treturn view('" . $val["action_name"] . "');\n";
						$str .= "\t\t}else{\n";
						$fieldList = model\Field::where(["menu_id" => $menu_id, "is_post" => 1])->order("sortid asc")->order("sortid asc")->select()->toArray();
						$relateFields = "";
						if ($val["relate_table"]) {
							$relateTableFieldList = service\BuildService::getRelateFieldList($val["relate_table"]);
							$fieldList = array_merge($fieldList, $relateTableFieldList);
							foreach ($relateTableFieldList as $k => $v) {
								if ($v["is_post"] == 1) {
									$relateFields .= $v["field"] . ",";
								}
							}
							$val["fields"] = $relateFields . $val["fields"];
						}
						$str .= "\t\t\t\$postField = '" . $menuInfo["pk_id"] . "," . str_replace("|", ",", $val["fields"]) . "';\n";
						$str .= "\t\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$data);\n";
						$str .= "\t\t\treturn json(['status'=>'00','msg'=>'修改成功']);\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
					}
					$relateFields = "";
					if ($val["is_view_create"] !== 0) {
						self::createInfoTpl($applicationInfo, $menuInfo, $val, service\BuildService::array_unset_tt($fieldList, "field"));
					}
					break;
				case in_array($val["type"], [5, 33]):
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$idx =  \$this->request->post('" . $pk_id . "', '', 'serach_in');\n";
						$str .= "\t\tif(!\$idx) \$this->error('参数错误');\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::destroy(['" . $pk_id . "'=>explode(',',\$idx)],true);\n";
						if ($val["relate_table"] && $val["relate_field"]) {
							$str .= "\t\t\tdb('" . $val["relate_table"] . "')->where(['" . $pk_id . "'=>explode(',',\$idx)])->delete();\n";
						}
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t}\n\n";
					}
					break;
				case 31:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$idx =  \$this->request->post('" . $pk_id . "', '', 'serach_in');\n";
						$str .= "\t\tif(!\$idx) \$this->error('参数错误');\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::destroy(['" . $pk_id . "'=>explode(',',\$idx)]);\n";
						if ($val["relate_table"] && $val["relate_field"]) {
							$str .= "\t\t\tdb('" . $val["relate_table"] . "')->where(['" . $pk_id . "'=>explode(',',\$idx)])->delete();\n";
						}
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t}\n\n";
					}
					break;
				case 6:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$idx =  \$this->request->post('" . $pk_id . "', '', 'serach_in');\n";
						$str .= "\t\tif(!\$idx) \$this->error('参数错误');\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::where(['" . $pk_id . "'=>explode(',',\$idx)])->update(['" . $val["fields"] . "'=>'" . $val["remark"] . "']);\n";
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t}\n\n";
					}
					break;
				case 7:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\tif (!\$this->request->isPost()){\n";
						$str .= "\t\t\t\$info['" . $pk_id . "'] = \$this->request->get('" . $pk_id . "','','serach_in');\n";
						$str .= "\t\t\treturn view('" . $val["action_name"] . "',['info'=>\$info]);\n";
						$str .= "\t\t}else{\n";
						$str .= "\t\t\t\$postField = '" . $pk_id . "," . $val["fields"] . "';\n";
						$str .= "\t\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= $token_str;
						$str .= "\t\t\tif(empty(\$data['" . $pk_id . "'])) \$this->error('参数错误');\n";
						$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(['" . $pk_id . "'=>explode(',',\$data['" . $pk_id . "'])],\$data);\n";
						$str .= "\t\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
					}
					if ($val["is_view_create"] !== 0) {
						self::createInfoTpl($applicationInfo, $menuInfo, $val, model\Field::where(["menu_id" => $menu_id, "is_post" => 1])->order("sortid asc")->select());
					}
					break;
				case 8:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\tif (!\$this->request->isPost()){\n";
						$str .= "\t\t\t\$info['" . $pk_id . "'] = \$this->request->get('" . $pk_id . "','','serach_in');\n";
						$str .= "\t\t\treturn view('" . $val["action_name"] . "',['info'=>\$info]);\n";
						$str .= "\t\t}else{\n";
						$str .= "\t\t\t\$postField = '" . $pk_id . "," . $val["fields"] . "';\n";
						$str .= "\t\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= $token_str;
						$str .= "\t\t\tif(empty(\$data['" . $pk_id . "'])) \$this->error('参数错误');\n";
						$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(['" . $pk_id . "'=>explode(',',\$data['" . $pk_id . "'])],\$data);\n";
						$str .= "\t\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
					}
					if ($val["is_view_create"] !== 0) {
						self::createInfoTpl($applicationInfo, $menuInfo, $val, model\Field::where(["menu_id" => $menu_id, "is_post" => 1])->order("sortid asc")->select());
					}
					break;
				case 9:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\tif (!\$this->request->isPost()){\n";
						$str .= "\t\t\t\$info['" . $pk_id . "'] = \$this->request->get('" . $pk_id . "','','serach_in');\n";
						$str .= "\t\t\treturn view('" . $val["action_name"] . "',['info'=>\$info]);\n";
						$str .= "\t\t}else{\n";
						$str .= "\t\t\t\$postField = '" . $pk_id . "," . $val["fields"] . "';\n";
						$str .= "\t\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= "\t\t\tif(empty(\$data['" . $pk_id . "'])) \$this->error('参数错误');\n";
						$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$data);\n";
						$str .= "\t\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
					}
					if ($val["is_view_create"] !== 0) {
						self::createInfoTpl($applicationInfo, $menuInfo, $val, model\Field::where(["menu_id" => $menu_id, "is_post" => 1])->order("sortid asc")->select());
					}
					break;
				case 12:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$fieldList = model\Field::where(["menu_id" => $menu_id])->order("sortid asc")->select();
						if ($fieldList) {
							$pre = "";
							$str .= "\t\t\$where = [];\n";
							if ($val["relate_table"] && $val["relate_field"] || strpos(strtolower($val["sql_query"]), "join") > 0) {
								$pre = "a.";
								$softDeleteAction = db("action")->where(["menu_id" => $menuInfo["menu_id"], "type" => 31])->value("action_name");
								if ($softDeleteAction) {
									if ($val["type"] == 12) {
										$str .= "\t\t\$where['" . $pre . "delete_time'] = ['exp','is null'];\n";
									}
									if ($val["type"] == 32) {
										$str .= "\t\t\$where['" . $pre . "delete_time'] = ['exp','is not null'];\n";
									}
								}
							}
							foreach ($fieldList as $k => $v) {
								if ($v["search_show"] == 1 && in_array($v["type"], [1, 2, 3, 4, 6, 7, 12, 13, 15, 17, 21, 23, 28, 29, 30]) || $v["type"] == 14) {
									if ($v["type"] == 4) {
										$str .= "\n";
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['find in set',\$this->request->param('" . $v["field"] . "', '', 'serach_in')];\n";
									} elseif ($v["type"] == 7) {
										$str .= "\n";
										$str .= "\t\t\$" . $v["field"] . "_start = \$this->request->param('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\$" . $v["field"] . "_end = \$this->request->param('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[strtotime(\$" . $v["field"] . "_start),strtotime(\$" . $v["field"] . "_end)]];\n";
									} elseif ($v["type"] == 12) {
										$str .= "\n";
										$str .= "\t\t\$" . $v["field"] . "_start = \$this->request->param('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\$" . $v["field"] . "_end = \$this->request->param('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[strtotime(\$" . $v["field"] . "_start),strtotime(\$" . $v["field"] . "_end)]];\n";
									} elseif ($v["type"] == 13) {
										$str .= "\n";
										$str .= "\t\t\$" . $v["field"] . "_start = \$this->request->param('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\$" . $v["field"] . "_end = \$this->request->param('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[\$" . $v["field"] . "_start,\$" . $v["field"] . "_end]];\n";
									} elseif ($v["type"] == 15) {
										if ($applicationInfo["app_id"] == 1) {
											$str .= "\t\tif(session('" . $applicationInfo["app_dir"] . ".role_id') <> 1){\n";
											$str .= "\t\t\t\$where['" . $pre . "" . $v["field"] . "'] = session('" . $applicationInfo["app_dir"] . "." . $v["field"] . "');\n";
											$str .= "\t\t}\n";
										} else {
											$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = session('" . $applicationInfo["app_dir"] . "." . $v["field"] . "');\n";
										}
									} elseif ($v["type"] == 17) {
										foreach (explode("|", $v["field"]) as $m => $n) {
											$str .= "\t\t\$where['" . $pre . "" . $n . "'] = \$this->request->param('" . $n . "', '', 'serach_in');\n";
										}
									} elseif ($v["search_type"]) {
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['like',\$this->request->param('" . $v["field"] . "', '', 'serach_in')];\n";
									} else {
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = \$this->request->param('" . $v["field"] . "', '', 'serach_in');\n";
									}
								}
							}
						}
						$str .= "\t\t\$where['" . $pre . "" . $pk_id . "'] = ['in',\$this->request->param('" . $pk_id . "', '', 'serach_in')];\n";
						$str .= "\n";
						$orderby = !empty($val["default_orderby"]) ? $val["default_orderby"] : $pk_id . " desc";
						$str .= "\t\ttry {\n";
						if (empty($val["relate_table"]) && empty($val["relate_field"]) && empty($val["sql_query"])) {
							$str .= "\t\t\t//此处读取前端传过来的 表格勾选的显示字段\n";
							$str .= "\t\t\t\$fieldInfo = [];\n";
							$str .= "\t\t\tfor(\$j=0; \$j<100;\$j++){\n";
							$str .= "\t\t\t\t\$fieldInfo[] = \$this->request->param(\$j);\n";
							$str .= "\t\t\t}\n";
							$str .= "\t\t\t\$list = " . getControllerName($menuInfo["controller_name"]) . "Model::where(formatWhere(\$where))->order('" . $orderby . "')->select();\n";
							$str .= "\t\t\tif(empty(\$list)) throw new Exception('没有数据');\n";
							$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(htmlOutList(\$list),filterEmptyArray(array_unique(\$fieldInfo)));\n";
						} else {
							if (!empty($val["sql_query"])) {
								$str .= "\t\t\t\$sql = '" . $val["sql_query"] . "';\n";
								if ($menuInfo["connect"]) {
									$str .= "\t\t\t\$res = \\xhadmin\\CommonService::loadList(\$sql,formatWhere(\$where),config('my.max_dump_data'),\$orderby='','" . $menuInfo["connect"] . "');\n";
								} else {
									$str .= "\t\t\t\$res = \\xhadmin\\CommonService::loadList(\$sql,formatWhere(\$where),config('my.max_dump_data'),\$orderby='');\n";
								}
							} else {
								$str .= "\t\t\t\$res['rows'] = db('" . $menuInfo["table_name"] . "')->field('" . $val["list_field"] . "')->alias('a')->join('" . $val["relate_table"] . " b','a." . $val["fields"] . "=b." . $val["relate_field"] . "','left')->where(formatWhere(\$where))->limit(config('my.max_dump_data'))->order('" . $orderby . "')->select();\n";
							}
							$str .= "\t\t\tif(empty(\$res['rows'])) throw new Exception('没有数据');\n";
							$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(htmlOutList(\$res['rows']));\n";
						}
						$str .= "\t\t} catch (\\Exception \$e) {\n";
						$str .= "\t\t\t\$this->error(\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
					}
					break;
				case 13:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\tif (\$this->request->isPost()) {\n";
						$str .= "\t\t\ttry{\n";
						$str .= "\t\t\t\t\$key = '" . getControllerName($menuInfo["controller_name"]) . "';\n";
						$str .= "\t\t\t\t\$result = \\xhadmin\\CommonService::importData(\$key);\n";
						$str .= "\t\t\t\tif (count(\$result) > 0) {\n";
						$str .= "\t\t\t\t\tcache(\$key,\$result,3600);\n";
						$str .= "\t\t\t\t\treturn redirect('startImport');\n";
						$str .= "\t\t\t\t} else{\n";
						$str .= "\t\t\t\t\t\$this->error('内容格式有误！');\n";
						$str .= "\t\t\t\t}\n";
						$str .= "\t\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\t\t\$this->error(\$e->getMessage());\n";
						$str .= "\t\t\t}\n";
						$str .= "\t\t}else {\n";
						$str .= "\t\t\treturn view('base/importData');\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
						$str .= "\t//开始导入\n";
						$str .= "\tfunction startImport(){\n";
						$str .= "\t\tif(!\$this->request->isPost()) {\n";
						$str .= "\t\t\treturn view('base/startImport');\n";
						$str .= "\t\t}else{\n";
						$str .= "\t\t\t\$p = \$this->request->post('p', '', 'intval'); \n";
						$str .= "\t\t\t\$data = cache('" . getControllerName($menuInfo["controller_name"]) . "');\n";
						$str .= "\t\t\t\$export_per_num = config('my.export_per_num') ? config('my.export_per_num') : 50;\n";
						$str .= "\t\t\t\$num = ceil((count(\$data)-1)/\$export_per_num);\n";
						if ($val["fields"]) {
							$str .= "\t\t\t\$export_fields = '" . $val["fields"] . "';\t//支持导入的字段\n";
						}
						$str .= "\t\t\tif(\$data){\n";
						$str .= "\t\t\t\t\$start = \$p == 1 ? 2 : (\$p-1) * \$export_per_num + 1;\n";
						$str .= "\t\t\t\tif(\$data[\$start]){\n";
						$str .= "\t\t\t\t\t\$dt['percent'] = ceil((\$p)/\$num*100);\n";
						$str .= "\t\t\t\t\ttry{\n";
						$str .= "\t\t\t\t\t\tfor(\$i=1; \$i<=\$export_per_num; \$i++ ){\n";
						$str .= "\t\t\t\t\t\t//根据中文名称来读取字段名称\n";
						$str .= "\t\t\t\t\t\t\tif(\$data[\$i + (\$p-1)*\$export_per_num]){\n";
						$str .= "\t\t\t\t\t\t\t\tforeach(\$data[1] as \$key=>\$val){\n";
						$str .= "\t\t\t\t\t\t\t\t\t\$fieldInfo = db('field')->where(['name'=>\$val,'menu_id'=>" . $menu_id . "])->find();\n";
						if ($val["fields"]) {
							$str .= "\t\t\t\t\t\t\t\t\tif(\$val && \$fieldInfo && in_array(\$fieldInfo['field'],explode(',',\$export_fields))){\n";
						} else {
							$str .= "\t\t\t\t\t\t\t\t\tif(\$val && \$fieldInfo){\n";
						}
						$str .= "\t\t\t\t\t\t\t\t\t\t\$d[\$fieldInfo['field']] = \$data[\$i + (\$p-1)*\$export_per_num][\$key];\n";
						$str .= "\t\t\t\t\t\t\t\t\t\tif(\$fieldInfo['type'] == 17){\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\tunset(\$d[\$fieldInfo['field']]);\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t\t\tif(in_array(\$fieldInfo['type'],[7,12])){\t//时间字段\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\tif(strlen(\$data[\$i + (\$p-1)*\$export_per_num][\$key]) == 5){\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\t\$d[\$fieldInfo['field']] = \\PhpOffice\\PhpSpreadsheet\\Shared\\Date::excelToTimestamp(\$data[\$i + (\$p-1)*\$export_per_num][\$key]);\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t}else{\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\t\$d[\$fieldInfo['field']] = strtotime(\$data[\$i + (\$p-1)*\$export_per_num][\$key]);\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t\t\tif(\$fieldInfo['type'] == 5){\t//密码字段\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\$d[\$fieldInfo['field']] = md5(\$data[\$i + (\$p-1)*\$export_per_num][\$key].config('my.password_secrect'));\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t\t\tif(\$fieldInfo['type'] == 17){\t//三级联动字段\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\$arrTitle = explode('|',\$fieldInfo['field']);\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\$arrValue = explode('-',\$data[\$i + (\$p-1)*\$export_per_num][\$key]);\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\tif(\$arrTitle && \$arrValue){\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\tforeach(\$arrTitle as \$k=>\$v){\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\$d[\$v] = \$arrValue[\$k];\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t\t\tif(in_array(\$fieldInfo['type'],[2,3,23,29]) && empty(\$fieldInfo['sql'])){\t//下拉，单选，开关按钮\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t\t\$d[\$fieldInfo['field']] = getFieldName(\$data[\$i + (\$p-1)*\$export_per_num][\$key],\$fieldInfo['config']);\n";
						$str .= "\t\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t\t}\n";
						$fieldList = model\Field::where(["menu_id" => $menu_id])->select();
						foreach ($fieldList as $key => $val) {
							if ($val["type"] == 12) {
								$timeField = $val["field"];
								$str .= "\t\t\t\t\t\t\t\t\$d['" . $val["field"] . "'] = time();\n";
							}
							if ($val["type"] == 15) {
								$str .= "\t\t\t\t\t\t\t\t\$d['" . $val["field"] . "'] = session('" . $applicationInfo["app_dir"] . "." . $val["field"] . "');\n";
							}
						}
						$str .= "\t\t\t\t\t\t\t\tif((\$i + (\$p-1)*\$export_per_num) > 1){\n";
						$str .= "\t\t\t\t\t\t\t\t\t" . $menuInfo["controller_name"] . "Model::create(\$d);\n";
						$str .= "\t\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\t\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t\t\t\t}\n";
						$str .= "\t\t\t\t\treturn json(['error'=>'00','data'=>\$dt]);\n";
						$str .= "\t\t\t\t}else{\n";
						$str .= "\t\t\t\t\tcache('" . getControllerName($menuInfo["controller_name"]) . "',null);\n";
						$str .= "\t\t\t\t\treturn json(['error'=>'10']);\n";
						$str .= "\t\t\t\t}\n";
						$str .= "\t\t\t}else{\n";
						$str .= "\t\t\t\t\$this->error('当前没有数据');\n";
						$str .= "\t\t\t}\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n";
					}
					break;
				case 14:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["block_name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\tif (!\$this->request->isPost()){\n";
						$str .= "\t\t\t\$" . $pk_id . " = \$this->request->get('" . $pk_id . "','','serach_in');\n";
						$str .= "\t\t\tif(!\$" . $pk_id . ") \$this->error('参数错误');\n";
						$str .= "\t\t\t\$this->view->assign('info',['" . $pk_id . "'=>\$" . $pk_id . "]);\n";
						$str .= "\t\t\treturn view('" . $val["action_name"] . "');\n";
						$str .= "\t\t}else{\n";
						$str .= "\t\t\t\$postField = '" . $menuInfo["pk_id"] . "," . str_replace("|", ",", $val["fields"]) . "';\n";
						$str .= "\t\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= "\t\t\t\$where['" . $menuInfo["pk_id"] . "'] = explode(',',\$data['" . $pk_id . "']);\n";
						$str .= "\t\t\tunset(\$data['" . $menuInfo["pk_id"] . "']);\n";
						$str .= "\t\t\ttry {\n";
						$fieldList = model\Field::where(["menu_id" => $menu_id])->order("sortid asc")->select();
						$fields = explode(",", $val["fields"]);
						if ($fields) {
							foreach ($fields as $k => $v) {
								foreach ($fieldList as $m => $n) {
									if ($v == $n["field"]) {
										if (in_array($n["type"], [7, 12, 25])) {
											$str .= "\t\t\t\t\$data['" . $v . "'] = strtotime(\$data['" . $v . "']);\n";
										}
										if ($n["type"] == 5) {
											if (config("my.password_secrect")) {
												$str .= "\t\t\t\$data['" . $v . "'] = md5(\$data['" . $v . "'].config('my.password_secrect'));\n";
											} else {
												$str .= "\t\t\t\$data['" . $v . "'] = md5(\$data['" . $v . "']);\n";
											}
										}
										if ($n["type"] == 15) {
											$fieldData .= "\t\t\t\$data['" . $v . "'] = session('" . $applicationInfo["app_dir"] . "." . $v . "');\n";
										}
										if ($n["type"] == 27) {
											$str .= "\t\t\t\t\$data['" . $v . "'] = implode(',',\$data['" . $v . "']);\n";
										}
									}
								}
							}
						}
						$str .= "\t\t\t\tdb('" . $menuInfo["table_name"] . "')->where(\$where)->update(\$data);\n";
						$str .= "\t\t\t} catch (\\Exception \$e) {\n";
						$str .= "\t\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t\t}\n";
						$str .= "\t\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t\t}\n";
						$str .= "\t}\n\n";
					}
					if ($val["is_view_create"] !== 0) {
						self::createInfoTpl($applicationInfo, $menuInfo, $val, model\Field::where(["menu_id" => $menu_id, "is_post" => 1])->order("sortid asc")->select());
					}
					break;
				case 15:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$" . $pk_id . " = \$this->request->get('" . $pk_id . "','','serach_in');\n";
						$str .= "\t\tif(!\$" . $menuInfo["pk_id"] . ") \$this->error('参数错误');\n";
						$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
						if (!empty($val["sql_query"])) {
							if (strpos(strtolower($val["sql_query"]), "join") > 0) {
								$pre_table = "a.";
							}
							$str .= "\t\t\$info = Db::connect('" . $connect . "')->query('" . $val["sql_query"] . " where " . $pre_table . "" . $pk_id . " = '.\$" . $pk_id . ");\n";
							$str .= "\t\t\$this->view->assign('info',current(\$info));\n";
						} else {
							if (!empty($val["relate_table"]) && !empty($val["relate_field"])) {
								if (!$val["list_field"]) {
									$field = "a.*,b.*";
								} else {
									$field = $val["list_field"];
								}
								$str .= "\t\t\$sql = 'select " . $field . " from " . config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"] . " as a left join " . config("database.connections." . $connect . ".prefix") . $val["relate_table"] . " as b on a." . $val["relate_field"] . " = b." . $val["relate_field"] . " where a." . $menuInfo["pk_id"] . " = '.\$" . $pk_id . ".' limit 1';\n";
								$str .= "\t\t\$info = Db::connect('" . $connect . "')->query(\$sql);\n";
								$str .= "\t\t\$this->view->assign('info',current(\$info));\n";
							} else {
								$str .= "\t\t\$this->view->assign('info'," . getControllerName($menuInfo["controller_name"]) . "Model::find(\$" . $pk_id . "));\n";
							}
						}
						$str .= "\t\treturn view('" . $val["action_name"] . "');\n";
						$str .= "\t}\n\n";
					}
					if ($val["is_view_create"] !== 0) {
						self::createViewTpl($applicationInfo, $menuInfo, $val, model\Field::where(["menu_id" => $menu_id])->order("sortid asc")->select());
					}
					break;
				case 16:
					if ($val["is_controller_create"] !== 0 && $updateExt_field) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$postField = '" . $pk_id . $updateExt_field . "';\n";
						$str .= "\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= "\t\tif(!\$data['" . $pk_id . "']) \$this->error('参数错误');\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::update(\$data);\n";
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t}\n\n";
					}
					break;
				case 30:
					if ($val["is_controller_create"] !== 0) {
						$sortidField = db("field")->where(["menu_id" => $menuInfo["menu_id"], "type" => 22])->value("field");
						$listActionInfo = db("action")->where(["menu_id" => $menuInfo["menu_id"], "type" => 1])->find();
						list($lgt_1, $orderby_1, $lgt_2, $orderby_2) = [">", "asc", "<", "desc"];
						if ($listActionInfo["default_orderby"]) {
							$default_orderby = strtolower($listActionInfo["default_orderby"]);
							if (preg_match("/" . $sortidField . "(.*)asc/", $default_orderby)) {
								list($lgt_1, $orderby_1, $lgt_2, $orderby_2) = ["<", "desc", ">", "asc"];
							}
						}
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$postField = '" . $pk_id . ",sortid,type';\n";
						$str .= "\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= "\t\tif(empty(\$data['sortid'])){\n";
						$str .= "\t\t\t\$this->error('操作失败，当前数据没有排序号');\n";
						$str .= "\t\t}\n";
						$treeInfo = db("action")->where(["menu_id" => $menuInfo["menu_id"], "type" => 1])->value("tree_config");
						if ($treeInfo) {
							$treePid = explode(",", $treeInfo)[0];
							$str .= "\t\t\$pid = " . getControllerName($menuInfo["controller_name"]) . "Model::where('" . $pk_id . "',\$data['" . $pk_id . "'])->value('" . $treePid . "');\n";
						}
						$str .= "\t\tif(\$data['type'] == 1){\n";
						if ($treePid) {
							$str .= "\t\t\t\$where['" . $treePid . "'] = \$pid;\n";
						}
						$str .= "\t\t\t\$where['" . $sortidField . "'] = ['" . $lgt_1 . "',\$data['sortid']];\n";
						$str .= "\t\t\t\$info = " . getControllerName($menuInfo["controller_name"]) . "Model::where(formatWhere(\$where))->order('" . $sortidField . " " . $orderby_1 . "')->find();\n";
						$str .= "\t\t}else{\n";
						if ($treePid) {
							$str .= "\t\t\t\$where['" . $treePid . "'] = \$pid;\n";
						}
						$str .= "\t\t\t\$where['" . $sortidField . "'] = ['" . $lgt_2 . "',\$data['sortid']];\n";
						$str .= "\t\t\t\$info = " . getControllerName($menuInfo["controller_name"]) . "Model::where(formatWhere(\$where))->order('" . $sortidField . " " . $orderby_2 . "')->find();\n";
						$str .= "\t\t}\n";
						$str .= "\t\tif(empty(\$info['" . $sortidField . "'])){\n";
						$str .= "\t\t\t\$this->error('操作失败，目标位置没有排序号');\n";
						$str .= "\t\t}\n";
						$str .= "\t\tif(\$info){\n";
						$str .= "\t\t\ttry{\n";
						$str .= "\t\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::update(['" . $pk_id . "'=>\$data['" . $pk_id . "'],'" . $sortidField . "'=>\$info['" . $sortidField . "']]);\n";
						$str .= "\t\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::update(['" . $pk_id . "'=>\$info['" . $pk_id . "'],'" . $sortidField . "'=>\$data['sortid']]);\n";
						$str .= "\t\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\t\tthrow new \\think\\exception\\ValidateException (\$e->getMessage());\n";
						$str .= "\t\t\t}\n";
						$str .= "\t\t}else{\n";
						$str .= "\t\t\t\$this->error('目标位置没有数据');\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t}\n\n";
					}
					break;
				case 34:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/*" . $val["name"] . "*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$idx =  \$this->request->post('" . $pk_id . "', '', 'serach_in');\n";
						$str .= "\t\tif(!\$idx) \$this->error('参数错误');\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t\$data = " . getControllerName($menuInfo["controller_name"]) . "Model::onlyTrashed()->where(['" . $pk_id . "'=>explode(',',\$idx)])->select();\n";
						$str .= "\t\t\tforeach(\$data as \$v){\n";
						$str .= "\t\t\t\t\$v->restore();\n";
						$str .= "\t\t\t}\n";
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn json(['status'=>'00','msg'=>'操作成功']);\n";
						$str .= "\t}\n\n";
					}
					break;
				default:
					$str .= service\ExtendService::getAdminExtendFuns($val, $fieldList);
			}
		}
		try {
			$rootPath = app()->getRootPath();
			$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/controller/" . $menuInfo["controller_name"] . ".php";
			filePutContents($str, $filepath, $type = 1);
			$this->createAdminService($actionList, $applicationInfo, $menuInfo);
			$this->createModel($actionList, $applicationInfo, $menuInfo);
			$this->createValidate($actionList, $applicationInfo, $menuInfo);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		return true;
	}
	public function createAdminService($actionList, $applicationInfo, $menuInfo)
	{
		if ($actionList) {
			$str = "";
			$str = "<?php \n";
			!is_null(config("my.comment.file_comment")) ? config("my.comment.file_comment") : true;
			if (config("my.comment.file_comment")) {
				$str .= "/*\n";
				$str .= " module:\t\t" . $menuInfo["title"] . "\n";
				$str .= " create_time:\t" . date("Y-m-d H:i:s") . "\n";
				$str .= " author:\t\t" . config("my.comment.author") . "\n";
				$str .= " contact:\t\t" . config("my.comment.contact") . "\n";
				$str .= "*/\n\n";
			}
			$str .= "namespace app\\" . $applicationInfo["app_dir"] . "\\service" . getDbName($menuInfo["controller_name"]) . ";\n";
			if ($menuInfo["table_name"]) {
				$str .= "use app\\" . $applicationInfo["app_dir"] . "\\model\\" . getUseName($menuInfo["controller_name"]) . ";\n";
			}
			$str .= "use think\\exception\\ValidateException;\n";
			$str .= "use xhadmin\\CommonService;\n";
			if ($actionList) {
				foreach ($actionList as $k => $v) {
					if ($v["type"] == 12) {
						$excelStatus = true;
					}
				}
			}
			if ($excelStatus) {
				$str .= "use PhpOffice\\PhpSpreadsheet\\Spreadsheet;\n";
				$str .= "use PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx;\n";
			}
			$str .= "\n";
			$str .= "class " . getControllerName($menuInfo["controller_name"]) . "Service extends CommonService {\n\n\n";
			$fieldList = htmlOutList(model\Field::where(["menu_id" => $menuInfo["menu_id"]])->order("sortid asc")->select()->toArray());
			foreach ($actionList as $key => $val) {
				if ($val["is_service_create"] !== 0) {
					switch ($val["type"]) {
						case 1:
							if (empty($val["sql_query"])) {
								$str .= "\t/*\n";
								$str .= " \t* @Description  " . $val["block_name"] . "列表数据\n";
								$str .= " \t*/\n";
								$str .= "\tpublic static function " . $val["action_name"] . "List(\$where,\$field,\$order,\$limit,\$page){\n";
								$str .= "\t\ttry{\n";
								if (!empty($val["fields"]) && !empty($val["relate_field"]) && !empty($val["relate_table"])) {
									if ($menuInfo["connect"]) {
										$str .= "\t\t\t\$res = db('" . $menuInfo["table_name"] . "','" . $menuInfo["connect"] . "')->field(\$field)->alias('a')->join('" . $val["relate_table"] . " b','a." . $val["fields"] . "=b." . $val["relate_field"] . "','left')->where(\$where)->order(\$order)->paginate(['list_rows'=>\$limit,'page'=>\$page])->toArray();\n";
									} else {
										$str .= "\t\t\t\$res = db('" . $menuInfo["table_name"] . "')->field(\$field)->alias('a')->join('" . $val["relate_table"] . " b','a." . $val["fields"] . "=b." . $val["relate_field"] . "','left')->where(\$where)->order(\$order)->paginate(['list_rows'=>\$limit,'page'=>\$page])->toArray();\n";
									}
								} else {
									$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->field(\$field)->order(\$order)->paginate(['list_rows'=>\$limit,'page'=>\$page])->toArray();\n";
								}
								$str .= "\t\t}catch(\\Exception \$e){\n";
								if (config("my.error_log_code")) {
									$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
								} else {
									$str .= "\t\t\tabort(500,\$e->getMessage());\n";
								}
								$str .= "\t\t}\n";
								$str .= "\t\treturn ['rows'=>\$res['data'],'total'=>\$res['total']];\n";
								$str .= "\t}\n\n\n";
							}
							break;
						case 32:
							if (empty($val["sql_query"])) {
								$str .= "\t/*\n";
								$str .= " \t* @Description  " . $val["block_name"] . "软删除列表数据\n";
								$str .= " \t*/\n";
								$str .= "\tpublic static function " . $val["action_name"] . "List(\$where,\$field,\$order,\$limit,\$page){\n";
								$str .= "\t\ttry{\n";
								if (!empty($val["fields"]) && !empty($val["relate_field"]) && !empty($val["relate_table"])) {
									$str .= "\t\t\t\$res = db('" . $menuInfo["table_name"] . "')->field(\$field)->alias('a')->join('" . $val["relate_table"] . " b','a." . $val["fields"] . "=b." . $val["relate_field"] . "','left')->where(\$where)->order(\$order)->paginate(['list_rows'=>\$limit,'page'=>\$page])->toArray();\n";
								} else {
									$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::onlyTrashed()->where(\$where)->field(\$field)->order(\$order)->paginate(['list_rows'=>\$limit,'page'=>\$page])->toArray();\n";
								}
								$str .= "\t\t}catch(\\Exception \$e){\n";
								if (config("my.error_log_code")) {
									$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
								} else {
									$str .= "\t\t\tabort(500,\$e->getMessage());\n";
								}
								$str .= "\t\t}\n";
								$str .= "\t\treturn ['rows'=>\$res['data'],'total'=>\$res['total']];\n";
								$str .= "\t}\n\n\n";
							}
							break;
						case 3:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb()->startTrans();\n\n";
							}
							foreach ($fieldList as $k => $v) {
								if (in_array($v["field"], explode(",", $val["fields"]))) {
									if ($v["type"] == 7) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = strtotime(\$data['" . $v["field"] . "']);\n";
									}
									if ($v["type"] == 5) {
										if (config("my.password_secrect")) {
											$str .= "\t\t\t\$data['" . $v["field"] . "'] = md5(\$data['" . $v["field"] . "'].config('my.password_secrect'));\n";
										} else {
											$str .= "\t\t\t\$data['" . $v["field"] . "'] = md5(\$data['" . $v["field"] . "']);\n";
										}
									}
									if ($v["type"] == 12) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = time();\n";
									}
									if ($v["type"] == 15) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = session('" . $applicationInfo["app_dir"] . "." . $v["field"] . "');\n";
									}
									if ($v["type"] == 21) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = random(" . $v["default_value"] . ",'all');\n";
									}
									if ($v["type"] == 26) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = request()->ip();\n";
									}
									if ($v["type"] == 27) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = implode(',',\$data['" . $v["field"] . "']);\n";
									}
									if ($v["type"] == 30) {
										$default_value = !empty($v["default_value"]) ? $v["default_value"] : "000";
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = doOrderSn('" . $default_value . "');\n";
									}
								}
							}
							$str .= $fieldData;
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::create(\$data);\n";
							if ($val["relate_table"]) {
								$str .= "\t\t\t\$data['" . $menuInfo["pk_id"] . "'] = \$res->" . $menuInfo["pk_id"] . ";\n";
								$str .= "\t\t\tdb('" . $val["relate_table"] . "')->insert(\$data);\n\n";
								$str .= "\t\t\tdb()->commit();\n";
							}
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb()->rollback();\n";
							}
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\tif(!\$res){\n";
							$str .= "\t\t\tthrow new ValidateException ('操作失败');\n";
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res->" . $menuInfo["pk_id"] . ";\n";
							$str .= "\t}\n\n\n";
							$rule = "";
							$msg = "";
							$fieldData = "";
							break;
						case 4:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb()->startTrans();\n\n";
							}
							foreach ($fieldList as $k => $v) {
								if (in_array($v["field"], explode(",", $val["fields"]))) {
									if (in_array($v["type"], [7, 12])) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = strtotime(\$data['" . $v["field"] . "']);\n";
									}
									if ($v["type"] == 15) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = session('" . $applicationInfo["app_dir"] . "." . $v["field"] . "');\n";
									}
									if ($v["type"] == 25) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = time();\n";
									}
									if ($v["type"] == 27) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = implode(',',\$data['" . $v["field"] . "']);\n";
									}
								}
							}
							$str .= $fieldData;
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::update(\$data);\n";
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb('" . $val["relate_table"] . "')->where('" . $menuInfo["pk_id"] . "',\$data['" . $menuInfo["pk_id"] . "'])->update(\$data);\n\n";
								$str .= "\t\t\tdb()->commit();\n";
							}
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb()->rollback();\n";
							}
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\tif(!\$res){\n";
							$str .= "\t\t\tthrow new ValidateException ('操作失败');\n";
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res;\n";
							$str .= "\t}\n\n\n";
							$rule = "";
							$msg = "";
							$field = "";
							$validate = "";
							$fieldData = "";
							break;
						case 7:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$where,\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->inc('" . $val["fields"] . "',\$data['" . $val["fields"] . "'])->update();\n";
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res;\n";
							$str .= "\t}\n\n\n";
							break;
						case 8:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$where,\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							$str .= "\t\t\t\$info = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->find();\n";
							$str .= "\t\t\tif(\$info->" . $val["fields"] . " < \$data['" . $val["fields"] . "']) throw new ValidateException('操作数据不足');\n";
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->dec('" . $val["fields"] . "',\$data['" . $val["fields"] . "'])->update();\n";
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res;\n";
							$str .= "\t}\n\n\n";
							break;
						case 9:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							if (config("my.password_secrect")) {
								$str .= "\t\t\t\$data['" . $val["fields"] . "'] = md5(\$data['" . $val["fields"] . "'].config('my.password_secrect'));\n";
							} else {
								$str .= "\t\t\t\$data['" . $val["fields"] . "'] = md5(\$data['" . $val["fields"] . "']);\n";
							}
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::update(\$data);\n";
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res;\n";
							$str .= "\t}\n\n\n";
							break;
						case 12:
							if (empty($val["relate_table"]) && empty($val["relate_field"]) && empty($val["sql_query"])) {
								$str .= "\t/*\n";
								$str .= " \t* @Description  " . $val["block_name"] . "\n";
								$str .= " \t*/\n";
								$str .= "\tpublic static function " . $val["action_name"] . "(\$list,\$field){\n";
								$str .= "\t\tob_clean();\n";
								$str .= "\t\ttry{\n";
								$str .= "\t\t\t\$map['menu_id'] = " . $menuInfo["menu_id"] . ";\n";
								$str .= "\t\t\t\$map['field'] = \$field;\n";
								$str .= "\t\t\t\$fieldList = db(\"field\")->where(\$map)->order('sortid asc')->select()->toArray();\n\n";
								$str .= "\t\t\t\$spreadsheet = new Spreadsheet();\n";
								$str .= "\t\t\t\$sheet = \$spreadsheet->getActiveSheet();\n";
								$str .= "\t\t\t//excel表头\n";
								$str .= "\t\t\tforeach(\$fieldList as \$key=>\$val){\n";
								$str .= "\t\t\t\t\$sheet->setCellValue(getTag(\$key+1).'1',\$val['name']);\n";
								$str .= "\t\t\t}\n";
								$str .= "\t\t\t//excel表主体内容\n";
								$str .= "\t\t\tforeach(\$list as \$k=>\$v){\n";
								$str .= "\t\t\t\tforeach(\$fieldList as \$m=>\$n){\n";
								$str .= "\t\t\t\t\tif(in_array(\$n['type'],[7,12,25]) && \$v[\$n['field']]){\n";
								$str .= "\t\t\t\t\t\t\$v[\$n['field']] = !empty(\$v[\$n['field']]) ? date(getTimeFormat(\$n),\$v[\$n['field']]) : '';\n";
								$str .= "\t\t\t\t\t}\n";
								$str .= "\t\t\t\t\tif(in_array(\$n['type'],[2,3,4,23,27,29]) && !empty(\$n['config'])){\n";
								$str .= "\t\t\t\t\t\t\$v[\$n['field']] = getFieldVal(\$v[\$n['field']],\$n['config']);\n";
								$str .= "\t\t\t\t\t}\n";
								$str .= "\t\t\t\t\tif(\$n['type'] == 17){\n";
								$str .= "\t\t\t\t\t\tforeach(explode('|',\$n['field']) as \$q){\n";
								$str .= "\t\t\t\t\t\t\t\$v[\$n['field']] .= \$v[\$q].'-';\n";
								$str .= "\t\t\t\t\t\t}\n";
								$str .= "\t\t\t\t\t\t\$v[\$n['field']] = rtrim(\$v[\$n['field']],'-');\n";
								$str .= "\t\t\t\t\t}\n";
								$str .= "\t\t\t\t\t\$sheet->setCellValueExplicit(getTag(\$m+1).(\$k+2),\$v[\$n['field']],\\PhpOffice\\PhpSpreadsheet\\Cell\\DataType::TYPE_STRING);\r\n";
								$str .= "\t\t\t\t\t\$v[\$n['field']] = '';\n";
								$str .= "\t\t\t\t}\n";
								$str .= "\t\t\t}\n";
							} else {
								$str .= "\t/*\n";
								$str .= " \t* @Description  " . $val["block_name"] . "\n";
								$str .= " \t* @param (输入参数：)  {array}        where 删除条件\n";
								$str .= " \t* @return (返回参数：) {bool}        \n";
								$str .= " \t*/\n";
								$str .= "\tpublic static function " . $val["action_name"] . "(\$list){\n";
								$str .= "\t\tob_clean();\n";
								$str .= "\t\ttry{\n";
								$str .= "\t\t\t\$spreadsheet = new Spreadsheet();\n";
								$str .= "\t\t\t\$sheet = \$spreadsheet->getActiveSheet();\n";
								$str .= "\t\t\t//excel表头\n";
								$i = 0;
								foreach ($fieldList as $key => $val) {
									if ($val["list_show"] == 1) {
										$i++;
										$str .= "\t\t\t\$sheet->setCellValue('" . getTag($i) . "1','" . $val["name"] . "');\n";
									}
								}
								$str .= "\n";
								$str .= "\t\t\t//excel表内容\n";
								$str .= "\t\t\tforeach(\$list as \$k=>\$v){\n";
								$j = 0;
								foreach ($fieldList as $key => $val) {
									if ($val["list_show"] == 1) {
										$j++;
										if (in_array($val["type"], [7, 12, 25])) {
											$str .= "\t\t\t\t\$v['" . $val["field"] . "'] = !empty(\$v['" . $val["field"] . "']) ? date('Y-m-d H:i:s',\$v['" . $val["field"] . "']) : '';\n";
										}
										if (in_array($val["type"], [2, 3, 4, 23, 27, 29]) && !empty($val["config"])) {
											$str .= "\t\t\t\t\$v['" . $val["field"] . "'] = getFieldVal(\$v['" . $val["field"] . "'],'" . $val["config"] . "');\n";
										}
										if ($val["type"] == 17) {
											foreach (explode("|", $val["field"]) as $k => $v) {
												$d .= "\$v['" . $v . "'].'-'.";
											}
											$d = rtrim($d, ".'-'.");
											$str .= "\t\t\t\t\$v['" . $val["field"] . "'] = " . $d . ";\n";
										}
										$str .= "\t\t\t\t\$sheet->setCellValue('" . getTag($j) . "'.(\$k+2),\$v['" . $val["field"] . "']);\n";
									}
									$d = "";
								}
								$str .= "\t\t\t}\n";
							}
							$str .= "\t\t\t\n";
							$str .= "\t\t\t\$filename = date('YmdHis');\n";
							$str .= "\t\t\theader('Content-Type: application/vnd.ms-excel');\n";
							$str .= "\t\t\theader('Content-Disposition: attachment;filename='.\$filename.'.'.config('my.import_type')); \n";
							$str .= "\t\t\theader('Cache-Control: max-age=0');\n";
							$str .= "\t\t\t\$writer = new Xlsx(\$spreadsheet); \n";
							$str .= "\t\t\t\$writer->save('php://output');\n";
							$str .= "\t\t\texit;\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							$str .= "\t\t\tthrow new \\Exception(\$e->getMessage());\n";
							$str .= "\t\t}\n";
							$str .= "\t}\n";
							break;
					}
				}
			}
			$rootPath = app()->getRootPath();
			$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/service/" . $menuInfo["controller_name"] . "Service.php";
			filePutContents($str, $filepath, $type = 1);
		}
	}
	public function createApiModule($menuInfo, $applicationInfo, $actionList)
	{
		$menu_id = $menuInfo["menu_id"];
		$pk_id = $menuInfo["pk_id"];
		$str = "";
		$str = "<?php \n";
		!is_null(config("my.comment.file_comment")) ? config("my.comment.file_comment") : true;
		if (config("my.comment.file_comment")) {
			$str .= "/*\n";
			$str .= " module:\t\t" . $menuInfo["title"] . "\n";
			$str .= " create_time:\t" . date("Y-m-d H:i:s") . "\n";
			$str .= " author:\t\t" . config("my.comment.author") . "\n";
			$str .= " contact:\t\t" . config("my.comment.contact") . "\n";
			$str .= "*/\n\n";
		}
		$str .= "namespace app\\" . $applicationInfo["app_dir"] . "\\controller" . getDbName($menuInfo["controller_name"]) . ";\n\n";
		$str .= "use app\\" . $applicationInfo["app_dir"] . "\\service\\" . getUseName($menuInfo["controller_name"]) . "Service;\n";
		if ($menuInfo["table_name"]) {
			$str .= "use app\\" . $applicationInfo["app_dir"] . "\\model\\" . getUseName($menuInfo["controller_name"]) . " as " . getControllerName($menuInfo["controller_name"]) . "Model;\n";
		}
		if (strpos($menuInfo["controller_name"], "/") > 0) {
			$str .= "use app\\" . $applicationInfo["app_dir"] . "\\controller\\Common;\n";
		}
		$str .= "use think\\exception\\ValidateException;\n";
		$str .= "use think\\facade\\Db;\n";
		$str .= "use think\\facade\\Log;\n";
		$str .= "\n";
		$str .= "class " . getControllerName($menuInfo["controller_name"]) . " extends Common {\n\n\n";
		$note_status = !is_null(config("my.comment.api_comment")) ? config("my.comment.api_comment") : true;
		$fieldList = model\Field::where(["menu_id" => $menu_id])->order("sortid asc")->select()->toArray();
		foreach ($fieldList as $k => $v) {
			$postFields[] = $v["field"];
		}
		foreach ($actionList as $key => $val) {
			foreach ($fieldList as $k => $v) {
				if ($v["type"] == 24 && $val["api_auth"]) {
					$token_str .= "\t\t\$data['" . $v["field"] . "'] = \$this->request->uid;\t//token解码用户ID\n";
					$token_field = $v["field"];
				}
			}
			switch ($val["type"]) {
				case 1:
					if ($val["is_controller_create"] !== 0) {
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "get";
						$str .= "\t/**\n";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
								$str .= "\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
								$str .= "\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\t* @apiParam (输入参数：) {int}     \t\t[limit] 每页数据条数（默认20）\n";
							$str .= "\t* @apiParam (输入参数：) {int}     \t\t[page] 当前页码\n";
							foreach ($fieldList as $k => $v) {
								if ($v["search_show"] == 1 && in_array($v["type"], [1, 2, 3, 4, 6, 7, 12, 13, 17, 21, 23, 27, 28, 29, 30]) && $v["type"] != 24) {
									if (in_array($v["type"], [2, 3, 4, 20, 22, 23])) {
										$fieldType = "{int}\t\t\t";
									} else {
										$fieldType = "{string}\t\t";
									}
									if ($v["type"] == 7) {
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "[" . $v["field"] . "_start] " . $v["name"] . "开始\n";
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "[" . $v["field"] . "_end] " . $v["name"] . "结束\n";
									} elseif ($v["type"] == 12) {
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "[" . $v["field"] . "_start] " . $v["name"] . "开始\n";
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "[" . $v["field"] . "_end] " . $v["name"] . "结束\n";
									} elseif ($v["type"] == 13) {
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "[" . $v["field"] . "_start] " . $v["name"] . "开始\n";
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "[" . $v["field"] . "_end] " . $v["name"] . "结束\n";
									} elseif ($v["type"] == 17) {
										foreach (explode("|", $v["field"]) as $m => $n) {
											switch ($m) {
												case 0:
													$disname = "省";
													break;
												case 1:
													$disname = "市";
													break;
												case 2:
													$disname = "区";
													break;
											}
											$str .= "\t* @apiParam (输入参数：) " . $fieldType . "[" . $n . "] " . $disname . "\n";
										}
									} else {
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "[" . $v["field"] . "] " . $v["name"] . " " . $v["config"] . "\n";
									}
								}
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.data 返回数据\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.data.list 返回数据列表\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.data.count 返回数据总数\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"data\":\"\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\" " . config("my.errorCode") . "\",\"msg\":\"查询失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$pagesize = !empty($val["pagesize"]) ? $val["pagesize"] : 20;
						if ($request_type == "post") {
							$str .= "\t\tif(!\$this->request->isPost()){\n";
							$str .= "\t\t\tthrow new ValidateException('请求错误');\n";
							$str .= "\t\t}\n";
						}
						$str .= "\t\t\$limit  = \$this->request->" . $request_type . "('limit', " . $pagesize . ", 'intval');\n";
						$str .= "\t\t\$page   = \$this->request->" . $request_type . "('page', 1, 'intval');\n\n";
						$str .= "\t\t\$where = [];\n";
						if ($fieldList) {
							foreach ($fieldList as $k => $v) {
								if ($v["search_show"] == 1 && in_array($v["type"], [1, 2, 3, 4, 6, 7, 12, 13, 17, 20, 21, 23, 24, 28, 29, 30]) || $v["type"] == 14) {
									$pre = "";
									if ($v["type"] == 4) {
										$str .= "\n";
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['find in set',\$this->request->param('" . $v["field"] . "', '', 'serach_in')];\n";
									} elseif ($v["type"] == 7) {
										$str .= "\n";
										$str .= "\t\t\$" . $v["field"] . "_start = \$this->request->" . $request_type . "('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\$" . $v["field"] . "_end = \$this->request->" . $request_type . "('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[strtotime(\$" . $v["field"] . "_start),strtotime(\$" . $v["field"] . "_end)]];\n";
									} elseif ($v["type"] == 12) {
										$str .= "\n";
										$str .= "\t\t\$" . $v["field"] . "_start = \$this->request->" . $request_type . "('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\$" . $v["field"] . "_end = \$this->request->" . $request_type . "('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[strtotime(\$" . $v["field"] . "_start),strtotime(\$" . $v["field"] . "_end)]];\n";
									} elseif ($v["type"] == 13) {
										$str .= "\n";
										$str .= "\t\t\$" . $v["field"] . "_start = \$this->request->" . $request_type . "('" . $v["field"] . "_start', '', 'serach_in');\n";
										$str .= "\t\t\$" . $v["field"] . "_end = \$this->request->" . $request_type . "('" . $v["field"] . "_end', '', 'serach_in');\n\n";
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['between',[\$" . $v["field"] . "_start,\$" . $v["field"] . "_end]];\n";
									} elseif ($v["type"] == 15) {
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = session('" . $applicationInfo["app_dir"] . "." . $v["field"] . "');\n";
									} elseif ($v["type"] == 17) {
										foreach (explode("|", $v["field"]) as $m => $n) {
											$str .= "\t\t\$where['" . $pre . "" . $n . "'] = \$this->request->" . $request_type . "('" . $n . "', '', 'serach_in');\n";
										}
									} elseif ($v["type"] == 24) {
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = \$this->request->uid;\t//token解码用户ID\n";
									} elseif ($v["search_type"]) {
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = ['like',\$this->request->" . $request_type . "('" . $v["field"] . "', '', 'serach_in')];\n";
									} else {
										$str .= "\t\t\$where['" . $pre . "" . $v["field"] . "'] = \$this->request->" . $request_type . "('" . $v["field"] . "', '', 'serach_in');\n";
									}
								}
							}
						}
						if ($val["relate_table"] && $val["relate_field"] || strpos(strtolower($val["sql_query"]), "join") > 0) {
							$pre = "a.";
							$softDeleteAction = db("action")->where(["menu_id" => $menuInfo["menu_id"], "type" => 31])->value("action_name");
							if ($softDeleteAction) {
								if ($val["type"] == 1) {
									$str .= "\t\t\$where['" . $pre . "delete_time'] = ['exp','is null'];\n";
								}
								if ($val["type"] == 32) {
									$str .= "\t\t\$where['" . $pre . "delete_time'] = ['exp','is not null'];\n";
								}
							}
						}
						$str .= "\n";
						if (!empty($val["relate_table"]) && !empty($val["relate_field"])) {
							if (!empty($val["list_field"])) {
								$str .= "\t\t\$field = '" . $val["list_field"] . "';\n";
							} else {
								$str .= "\t\t\$field = 'a.*,b.*';\n";
							}
						} else {
							if (!empty($val["fields"])) {
								$str .= "\t\t\$field = '" . str_replace("|", ",", $val["fields"]) . "';\n";
							} else {
								$str .= "\t\t\$field = '*';\n";
							}
						}
						if (!empty($val["default_orderby"])) {
							$str .= "\t\t\$orderby = '" . $val["default_orderby"] . "';\n\n";
						} else {
							$str .= "\t\t\$orderby = '" . $pk_id . " desc';\n\n";
						}
						if ($val["cache_time"]) {
							$str .= "\t\t\$key = md5(implode(',',\$where).\$limit.\$field.\$orderby);\n";
							$str .= "\t\tif(cache(\$key)){\n";
							$str .= "\t\t\t\$res = cache(\$key);\n";
							$str .= "\t\t}else{\n";
						}
						$s = empty($val["cache_time"]) ? "\t\t" : "\t\t\t";
						if (!empty($val["sql_query"])) {
							if (!strpos($val["sql_query"], ".")) {
								$val["sql_query"] = str_replace("'", "\"", $val["sql_query"]);
							}
							$str .= $s . "\$sql = '" . str_replace(["\r\n", "\r", "\n"], " ", $val["sql_query"]) . "';\n";
							$str .= "\t\t\$limit = (\$page-1) * \$limit.','.\$limit;\n";
							if ($menuInfo["connect"]) {
								$str .= "\t\t\$res = \\xhadmin\\CommonService::loadList(\$sql,formatWhere(\$where),\$limit,\$orderby,'" . $menuInfo["connect"] . "');\n";
							} else {
								$str .= "\t\t\$res = \\xhadmin\\CommonService::loadList(\$sql,formatWhere(\$where),\$limit,\$orderby);\n";
							}
						} else {
							$str .= $s . "\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "List(formatWhere(\$where),\$field,\$orderby,\$limit,\$page);\n";
						}
						if ($val["cache_time"]) {
							$str .= "\t\t\tcache(\$key,\$res," . $val["cache_time"] . ");\n";
							$str .= "\t\t}\n";
						}
						if (!empty($val["tree_config"])) {
							$tree_config = explode(",", $val["tree_config"]);
							$str .= "\t\t\$res['list'] = formartList(['" . $pk_id . "', '" . $tree_config[0] . "', '" . $tree_config[1] . "','" . $tree_config[1] . "'],\$res['list']);\n";
						}
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'返回成功',htmlOutList(\$res));\n";
						$str .= "\t}\n\n";
						$token_str = "";
					}
					break;
				case 3:
					if ($val["is_controller_create"] !== 0) {
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t/**\n";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							foreach (explode(",", $val["fields"]) as $k => $v) {
								$fieldInfo = model\Field::where(["field" => $v, "menu_id" => $menuInfo["menu_id"]])->find();
								if (!in_array($fieldInfo["type"], [12, 24])) {
									if (in_array($fieldInfo["type"], [2, 3, 4, 20, 22, 23])) {
										$fieldType = "{int}\t\t\t";
									} else {
										$fieldType = "{string}\t\t";
									}
									if ($fieldInfo["type"] == 17) {
										foreach (explode("|", $fieldInfo["field"]) as $m => $n) {
											switch ($m) {
												case 0:
													$disname = "省";
													break;
												case 1:
													$disname = "市";
													break;
												case 2:
													$disname = "区";
													break;
											}
											$str .= "\t* @apiParam (输入参数：) " . $fieldType . "\t" . $n . " " . $disname . "\n";
										}
									} else {
										!empty($fieldInfo["validate"]) && ($fieldInfo["name"] = $fieldInfo["name"] . " (必填)");
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "\t" . $v . " " . $fieldInfo["name"] . " " . $fieldInfo["config"] . "\n";
									}
								}
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码  " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"data\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\" " . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$fields = explode(",", $val["fields"]);
						foreach ($fields as $k => $v) {
							if (in_array($v, $postFields)) {
								$showFields .= "," . $v;
							}
						}
						$showFields = ltrim($showFields, ",");
						$str .= "\t\t\$postField = '" . str_replace("|", ",", $showFields) . "';\n";
						$str .= "\t\t\$data = \$this->request->only(explode(',',\$postField),'" . $request_type . "',null);\n";
						$str .= $token_str;
						$str .= "\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$data);\n";
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'操作成功',\$res);\n";
						$str .= "\t}\n\n";
						$token_str = "";
						$fields = "";
						$showFields = "";
					}
					break;
				case 4:
					if ($val["is_controller_create"] !== 0) {
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t/**\n";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$fieldInfo = model\Field::where(["field" => $pk_id, "menu_id" => $menuInfo["menu_id"]])->find();
							if ($fieldInfo["type"] != 24 || !$val["api_auth"]) {
								$str .= "\t\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $pk_id . " 主键ID (必填)\n";
							}
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							foreach (explode(",", $val["fields"]) as $k => $v) {
								$fieldInfo = model\Field::where(["field" => $v, "menu_id" => $menuInfo["menu_id"]])->find();
								if (in_array($fieldInfo["type"], [2, 3, 4, 20, 22, 23])) {
									$fieldType = "{int}\t\t\t";
								} else {
									$fieldType = "{string}\t\t";
								}
								if ($fieldInfo["type"] == 17) {
									foreach (explode("|", $fieldInfo["field"]) as $m => $n) {
										switch ($m) {
											case 0:
												$disname = "省";
												break;
											case 1:
												$disname = "市";
												break;
											case 2:
												$disname = "区";
												break;
										}
										$str .= "\t* @apiParam (输入参数：) " . $fieldType . "\t" . $n . " " . $disname . "\n";
									}
								} else {
									!empty($fieldInfo["validate"]) && ($fieldInfo["name"] = $fieldInfo["name"] . " (必填)");
									$str .= "\t* @apiParam (输入参数：) " . $fieldType . "\t" . $v . " " . $fieldInfo["name"] . " " . $fieldInfo["config"] . "\n";
								}
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码  " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\" " . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$fields = explode(",", $val["fields"]);
						foreach ($fields as $k => $v) {
							if (in_array($v, $postFields)) {
								$showFields .= "," . $v;
							}
						}
						$showFields = ltrim($showFields, ",");
						$str .= "\t\t\$postField = '" . $menuInfo["pk_id"] . "," . str_replace("|", ",", $showFields) . "';\n";
						$str .= "\t\t\$data = \$this->request->only(explode(',',\$postField),'" . $request_type . "',null);\n";
						$str .= $token_str;
						$str .= "\t\tif(empty(\$data['" . $pk_id . "'])){\n";
						$str .= "\t\t\tthrow new ValidateException('参数错误');\n";
						$str .= "\t\t}\n";
						$str .= "\t\t\$where['" . $pk_id . "'] = \$data['" . $pk_id . "'];\n";
						if ($token_str && $token_field != $pk_id) {
							$str .= "\t\t\$where['" . $token_field . "'] = \$data['" . $token_field . "'];\n";
						}
						$str .= "\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$where,\$data);\n";
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'操作成功');\n";
						$str .= "\t}\n\n";
						$token_str = "";
						$fields = "";
						$showFields = "";
					}
					break;
				case 5:
					if ($val["is_controller_create"] !== 0) {
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t/**\n";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $pk_id . "s 主键id 注意后面跟了s 多数据删除\n";
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$idx =  \$this->request->post('" . $pk_id . "s', '', 'serach_in');\n";
						$str .= "\t\tif(empty(\$idx)){\n";
						$str .= "\t\t\tthrow new ValidateException('参数错误');\n";
						$str .= "\t\t}\n";
						if ($token_str && $token_field != $pk_id) {
							$str .= "" . $token_str;
						}
						$str .= "\t\t\$data['" . $pk_id . "'] = explode(',',\$idx);\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::destroy(\$data,true);\n";
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'操作成功');\n";
						$str .= "\t}\n\n";
						$token_str = "";
					}
					break;
				case 31:
					if ($val["is_controller_create"] !== 0) {
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t/**\n";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $pk_id . "s 主键id 注意后面跟了s 多数据删除\n";
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$idx =  \$this->request->post('" . $pk_id . "s', '', 'serach_in');\n";
						$str .= "\t\tif(empty(\$idx)){\n";
						$str .= "\t\t\tthrow new ValidateException('参数错误');\n";
						$str .= "\t\t}\n";
						if ($token_str && $token_field != $pk_id) {
							$str .= "" . $token_str;
						}
						$str .= "\t\t\$data['" . $pk_id . "'] = explode(',',\$idx);\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t" . getControllerName($menuInfo["controller_name"]) . "Model::destroy(\$data);\n";
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'操作成功');\n";
						$str .= "\t}\n\n";
						$token_str = "";
					}
					break;
				case 6:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/**\n";
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$fieldInfo = model\Field::where(["field" => $pk_id, "menu_id" => $menuInfo["menu_id"]])->find();
							if ($fieldInfo["type"] != 24 || !$val["api_auth"]) {
								$str .= "\t\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $pk_id . " 主键ID\n";
							}
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$data['" . $pk_id . "'] = \$this->request->" . $request_type . "('" . $pk_id . "','','intval');\n";
						$str .= $token_str;
						$str .= "\t\tif(empty(\$data['" . $pk_id . "'])){\n";
						$str .= "\t\t\tthrow new ValidateException('参数错误');\n";
						$str .= "\t\t}\n";
						if ($token_str && $token_field != $pk_id) {
							$str .= "\t\t\$where['" . $token_field . "'] = \$data['" . $token_field . "'];\n";
						}
						$str .= "\t\t\$where['" . $pk_id . "'] = \$data['" . $pk_id . "'];\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Model::where(\$where)->update(['" . $val["fields"] . "'=>'" . $val["remark"] . "']);\n";
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'操作成功');\n";
						$str .= "\t}\n\n";
						$token_str = "";
					}
					break;
				case 7:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/**\n";
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$fieldInfo = model\Field::where(["field" => $pk_id, "menu_id" => $menuInfo["menu_id"]])->find();
							if ($fieldInfo["type"] != 24 || !$val["api_auth"]) {
								$str .= "\t\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $pk_id . " 主键ID\n";
							}
							$str .= "\t* @apiParam (输入参数：) {float}     \t\t" . $val["fields"] . " 充值积分\n";
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.data 返回自增ID\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$postField = '" . $pk_id . "," . $val["fields"] . "';\n";
						$str .= "\t\t\$data = \$this->request->only(explode(',',\$postField),'" . $request_type . "',null);\n";
						$str .= $token_str;
						$str .= "\t\tif(empty(\$data['" . $pk_id . "'])){\n";
						$str .= "\t\t\tthrow new ValidateException('参数错误');\n";
						$str .= "\t\t}\n";
						if ($token_str && $token_field != $pk_id) {
							$str .= "\t\t\$where['" . $token_field . "'] = \$data['" . $token_field . "'];\n";
						}
						$str .= "\t\t\$where['" . $pk_id . "'] = (int) \$data['" . $pk_id . "'];\n";
						$str .= "\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$where,\$data);\n";
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'操作成功');\n";
						$str .= "\t}\n\n";
						$token_str = "";
					}
					break;
				case 8:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/**\n";
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$fieldInfo = model\Field::where(["field" => $pk_id, "menu_id" => $menuInfo["menu_id"]])->find();
							if ($fieldInfo["type"] != 24 || !$val["api_auth"]) {
								$str .= "\t\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $pk_id . " 主键ID\n";
							}
							$str .= "\t* @apiParam (输入参数：) {float}     \t\t" . $val["fields"] . " 回收积分\n";
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$postField = '" . $pk_id . "," . $val["fields"] . "';\n";
						$str .= "\t\t\$data = \$this->request->only(explode(',',\$postField),'" . $request_type . "',null);\n";
						$str .= $token_str;
						$str .= "\t\tif(empty(\$data['" . $pk_id . "'])){\n";
						$str .= "\t\t\tthrow new ValidateException('参数错误');\n";
						$str .= "\t\t}\n";
						if ($token_str && $token_field != $pk_id) {
							$str .= "\t\t\$where['" . $token_field . "'] = \$data['" . $token_field . "'];\n";
						}
						$str .= "\t\t\$where['" . $pk_id . "'] = (int) \$data['" . $pk_id . "'];\n";
						$str .= "\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$where,\$data);\n";
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'操作成功');\n";
						$str .= "\t}\n\n";
						$token_str = "";
					}
					break;
				case 9:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/**\n";
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$fieldInfo = model\Field::where(["field" => $pk_id, "menu_id" => $menuInfo["menu_id"]])->find();
							if ($fieldInfo["type"] != 24 || !$val["api_auth"]) {
								$str .= "\t\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $pk_id . " 主键ID\n";
							}
							$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $val["fields"] . " 新密码(必填)\n";
							$str .= "\t* @apiParam (输入参数：) {string}     \t\tre" . $val["fields"] . " 重复密码(必填)\n";
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$postField = '" . $pk_id . "," . $val["fields"] . ",re" . $val["fields"] . "';\n";
						$str .= "\t\t\$data = \$this->request->only(explode(',',\$postField),'" . $request_type . "',null);\n";
						$str .= $token_str;
						$str .= "\t\tif(empty(\$data['" . $pk_id . "'])){\n";
						$str .= "\t\t\tthrow new ValidateException('参数错误');\n";
						$str .= "\t\t}\n";
						$str .= "\t\tif(empty(\$data['" . $val["fields"] . "'])){ \n";
						$str .= "\t\t\tthrow new ValidateException('密码不能为空');\n";
						$str .= "\t\t}\n";
						$str .= "\t\tif(\$data['" . $val["fields"] . "'] <> \$data['re" . $val["fields"] . "']){ \n";
						$str .= "\t\t\tthrow new ValidateException('两次密码输入不一致');\n";
						$str .= "\t\t}\n";
						$str .= "\t\t\$where['" . $pk_id . "'] = \$data['" . $pk_id . "'];\n";
						if ($token_str && $token_field != $pk_id) {
							$str .= "\t\t\$where['" . $token_field . "'] = \$data['" . $token_field . "'];\n";
						}
						$str .= "\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$where,\$data);\n";
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'操作成功');\n";
						$str .= "\t}\n\n";
						$token_str = "";
					}
					break;
				case 15:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/**\n";
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "get";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$fieldInfo = model\Field::where(["field" => $pk_id, "menu_id" => $menuInfo["menu_id"]])->find();
							if ($fieldInfo["type"] != 24 || !$val["api_auth"]) {
								$str .= "\t\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $pk_id . " 主键ID\n";
							}
							if ($val["remark"]) {
								foreach (explode("|", $val["remark"]) as $k => $n) {
									$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $n . " 主键ID\n";
								}
							}
							if ($val["api_auth"]) {
								$str .= "\n";
								$str .= "\t* @apiHeader {String} Authorization 用户授权token\n";
								$str .= "\t* @apiHeaderExample {json} Header-示例:\n";
								$str .= "\t* \"Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org\"\n";
							}
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.data 返回数据详情\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"data\":\"\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"没有数据\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= $token_str;
						if ($val["remark"]) {
							foreach (explode("|", $val["remark"]) as $k => $n) {
								$str .= "\t\t\$data['" . $n . "'] = \$this->request->" . $request_type . "('" . $n . "','','serach_in');\n";
							}
						} else {
							if (!(service\BuildService::getFieldType($pk_id, $menu_id) == 24 && $val["api_auth"])) {
								$str .= "\t\t\$data['" . $pk_id . "'] = \$this->request->" . $request_type . "('" . $pk_id . "','','serach_in');\n";
							}
						}
						if ($val["cache_time"]) {
							$str .= "\t\t\$key = md5('" . getControllerName($menuInfo["controller_name"]) . ":" . $pk_id . ":'.\$data['" . $pk_id . "']);\n";
							$str .= "\t\tif(cache(\$key)){\n";
							$str .= "\t\t\t\$res = cache(\$key);\n";
							$str .= "\t\t}else{\n";
						}
						$s = empty($val["cache_time"]) ? "\t\t" : "\t\t\t";
						if (!empty($val["sql_query"])) {
							if (strpos($val["sql_query"], "join") > 0) {
								$pre_table = "a.";
							}
							$str .= $s . "\$res = checkData(Db::query('" . $val["sql_query"] . " where " . $pre_table . $pk_id . " = '.\$data['" . $pk_id . "']));\n";
						} else {
							if (!empty($val["relate_table"]) && !empty($val["relate_field"])) {
								if (!$val["list_field"]) {
									$field = "a.*,b.*";
								} else {
									$field = $val["list_field"];
								}
								$connect = $menuInfo["connect"] ? $menuInfo["connect"] : config("database.default");
								$str .= $s . "\$sql = 'select " . $field . " from " . config("database.connections." . $connect . ".prefix") . $menuInfo["table_name"] . " as a left join " . config("database.connections." . $connect . ".prefix") . $val["relate_table"] . " as b on a." . $val["relate_field"] . " = b." . $val["relate_field"] . " where a." . $menuInfo["pk_id"] . " = '.\$data['" . $pk_id . "'].' limit 1';\n";
								$str .= $s . "\$res = checkData(current(Db::connect('" . $connect . "')->query(\$sql)));\n";
							} else {
								if (empty($val["fields"])) {
									$str .= $s . "\$res  = checkData(" . getControllerName($menuInfo["controller_name"]) . "Model::find(\$data['" . $pk_id . "']));\n";
								} else {
									$fields = explode(",", $val["fields"]);
									foreach ($fields as $k => $v) {
										if (in_array($v, $postFields)) {
											$showFields .= "," . $v;
										}
									}
									$showFields = ltrim($showFields, ",");
									$str .= $s . "\$field='" . $pk_id . "," . str_replace("|", ",", $showFields) . "';\n";
									$str .= $s . "\$res  = checkData(" . getControllerName($menuInfo["controller_name"]) . "Model::field(\$field)->where(\$data)->find());\n";
								}
							}
						}
						if ($val["cache_time"]) {
							$str .= "\t\t\tcache(\$key,\$res," . $val["cache_time"] . ");\n";
							$str .= "\t\t}\n";
						}
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'返回成功',\$res);\n";
						$str .= "\t}\n\n";
						$token_str = "";
						$fields = "";
						$showFields = "";
					}
					break;
				case 17:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/**\n";
						$str .= "\t* @api {post} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$str .= "\t\n";
							if (!empty($val["remark"])) {
								list($username, $password, $uid) = explode("|", $val["remark"]);
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $username . " 登录用户名\n";
							$str .= "\t* @apiParam (输入参数：) {string}     \t\t" . $password . " 登录密码\n";
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						if (!empty($val["remark"])) {
							list($username, $password, $uid) = explode("|", $val["remark"]);
						}
						$str .= "\t\t\$postField = '" . $username . "," . $password . "';\n";
						$str .= "\t\t\$data = \$this->request->only(explode(',',\$postField),'post',null);\n";
						$str .= "\t\tif(empty(\$data['" . $username . "']) || empty(\$data['" . $password . "'])) throw new ValidateException('账号或密码不能为空');\n";
						if (empty($val["fields"])) {
							$str .= "\t\t\$returnField = '*';\n";
						} else {
							$str .= "\t\t\$returnField = '" . $pk_id . "," . str_replace("|", ",", $val["fields"]) . "';\n";
						}
						$str .= "\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$data,\$returnField);\n";
						if (empty($uid)) {
							$uid = $pk_id;
						}
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'登陆成功',\$res,\$this->setToken(\$res['" . $uid . "']));\n";
						$str .= "\t}\n\n";
					}
					break;
				case 19:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/**\n";
						$str .= "\t* @api {post} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$str .= "\t\n";
							if (!empty($val["remark"])) {
								list($username, $uid) = explode("|", $val["remark"]);
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 登录手机号\n";
							if ($val["sms_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 短信验证手机号\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify_id 短信验证ID\n";
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tverify 短信验证码\n";
							}
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$mobile = \$this->request->post('mobile','post',null);\n";
						$str .= "\t\tif(empty(\$mobile)) throw new ValidateException('手机号不能为空');\n";
						if (empty($val["fields"])) {
							$str .= "\t\t\$returnField = '*';\n";
						} else {
							$str .= "\t\t\$returnField = '" . $pk_id . "," . str_replace("|", ",", $val["fields"]) . "';\n";
						}
						$str .= "\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "Service::" . $val["action_name"] . "(\$mobile,\$returnField);\n";
						if (empty($uid)) {
							$uid = $pk_id;
						}
						$str .= "\t\treturn \$this->ajaxReturn(\$this->successCode,'登陆成功',\$res,\$this->setToken(\$res['" . $uid . "']));\n";
						$str .= "\t}\n\n";
					}
					break;
				case 18:
					if ($val["is_controller_create"] !== 0) {
						$str .= "\t/**\n";
						$request_type = !empty($val["request_type"]) ? $val["request_type"] : "post";
						$str .= "\t* @api {" . $request_type . "} /" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . " " . sprintf("%02d", $key + 1) . "、" . $val["name"] . "\n";
						if ($note_status) {
							$str .= "\t* @apiGroup " . getControllerName($menuInfo["controller_name"]) . "\n";
							$str .= "\t* @apiVersion 1.0.0\n";
							$description = !empty($val["block_name"]) ? $val["block_name"] : $val["name"];
							$str .= "\t* @apiDescription  " . $description . "\n";
							$str .= "\t\n";
							if (!empty($val["remark"])) {
								list($username, $password, $uid) = explode("|", $val["remark"]);
							}
							if ($val["captcha_auth"]) {
								$str .= "\t* @apiParam (输入参数：) {string}     \t\tcaptcha 图片验证码\n";
								$str .= "\n";
							}
							$str .= "\t* @apiParam (输入参数：) {string}     \t\tmobile 手机号\n";
							$str .= "\n";
							$str .= "\t* @apiParam (失败返回参数：) {object}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.status 返回错误码 " . config("my.errorCode") . "\n";
							$str .= "\t* @apiParam (失败返回参数：) {string}     \tarray.msg 返回错误消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray 返回结果集\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.status 返回错误码 " . config("my.successCode") . "\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.msg 返回成功消息\n";
							$str .= "\t* @apiParam (成功返回参数：) {string}     \tarray.key 返回短信验证ID\n";
							$str .= "\t* @apiSuccessExample {json} 01 成功示例\n";
							$str .= "\t* {\"status\":\"" . config("my.successCode") . "\",\"msg\":\"操作成功\"}\n";
							$str .= "\t* @apiErrorExample {json} 02 失败示例\n";
							$str .= "\t* {\"status\":\"" . config("my.errorCode") . "\",\"msg\":\"操作失败\"}\n";
						}
						$str .= "\t*/\n";
						$str .= "\tfunction " . $val["action_name"] . "(){\n";
						$str .= "\t\t\$mobile = \$this->request->" . $request_type . "('mobile');\n";
						$str .= "\t\tif(empty(\$mobile)) throw new ValidateException ('手机号不能为空');\n";
						$str .= "\t\tif(!preg_match('/^1[3456789]\\d{9}\$/',\$mobile)) throw new ValidateException ('手机号格式错误');\n";
						$str .= "\t\ttry{\n";
						$str .= "\t\t\t\$data['mobile']\t= \$mobile;\t//发送手机号\n";
						$str .= "\t\t\t\$data['code']\t= sprintf('%06d', rand(0,999999));\t\t//验证码\n";
						if (empty($val["remark"])) {
							$str .= "\t\t\t\$res = \\utils\\sms\\AliSmsService::sendSms(\$data);\n";
						} else {
							$str .= "\t\t\t\$res = \\utils\\sms\\" . $val["remark"] . "SmsService::sendSms(\$data);\n";
						}
						$str .= "\t\t}catch(\\Exception \$e){\n";
						$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
						$str .= "\t\t}\n";
						$str .= "\t\t\$key = md5(time().\$data['mobile']);\n";
						$str .= "\t\tcache(\$key,['mobile'=>\$data['mobile'],'code'=>\$data['code']]," . $val["cache_time"] . ");\n";
						$str .= "\t\treturn json(['status'=>\$this->successCode,'msg'=>'发送成功','key'=>\$key]);\n";
						$str .= "\t}\n\n";
					}
					break;
				default:
					$str .= service\ExtendService::getApiExtendFuns($val, $fieldList);
			}
		}
		try {
			$rootPath = app()->getRootPath();
			$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/controller/" . $menuInfo["controller_name"] . ".php";
			filePutContents($str, $filepath, $type = 1);
			$this->createApiService($actionList, $applicationInfo, $menuInfo);
			$this->createModel($actionList, $applicationInfo, $menuInfo);
			$this->createValidate($actionList, $applicationInfo, $menuInfo);
			$this->createRoute($applicationInfo);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
		return true;
	}
	public function createApiService($actionList, $applicationInfo, $menuInfo)
	{
		if ($actionList) {
			$str = "";
			$str = "<?php \n";
			!is_null(config("my.comment.file_comment")) ? config("my.comment.file_comment") : true;
			if (config("my.comment.file_comment")) {
				$str .= "/*\n";
				$str .= " module:\t\t" . $menuInfo["title"] . "\n";
				$str .= " create_time:\t" . date("Y-m-d H:i:s") . "\n";
				$str .= " author:\t\t" . config("my.comment.author") . "\n";
				$str .= " contact:\t\t" . config("my.comment.contact") . "\n";
				$str .= "*/\n\n";
			}
			$str .= "namespace app\\" . $applicationInfo["app_dir"] . "\\service" . getDbName($menuInfo["controller_name"]) . ";\n";
			if ($menuInfo["table_name"]) {
				$str .= "use app\\" . $applicationInfo["app_dir"] . "\\model\\" . getUseName($menuInfo["controller_name"]) . ";\n";
			}
			$str .= "use think\\facade\\Log;\n";
			$str .= "use think\\exception\\ValidateException;\n";
			$str .= "use xhadmin\\CommonService;\n";
			$str .= "\n";
			$str .= "class " . getControllerName($menuInfo["controller_name"]) . "Service extends CommonService {\n\n\n";
			$fieldList = htmlOutList(model\Field::where(["menu_id" => $menuInfo["menu_id"]])->select());
			foreach ($actionList as $key => $val) {
				if ($val["is_service_create"] !== 0) {
					switch ($val["type"]) {
						case 1:
							if (empty($val["sql_query"])) {
								$str .= "\t/*\n";
								$str .= " \t* @Description  " . $val["block_name"] . "列表数据\n";
								$str .= " \t*/\n";
								$str .= "\tpublic static function " . $val["action_name"] . "List(\$where,\$field,\$orderby,\$limit,\$page){\n";
								$str .= "\t\ttry{\n";
								if (!empty($val["fields"]) && !empty($val["relate_field"]) && !empty($val["relate_table"])) {
									if ($menuInfo["connect"]) {
										$str .= "\t\t\t\$res = db('" . $menuInfo["table_name"] . "','" . $menuInfo["connect"] . "')->field(\$field)->alias('a')->join('" . $val["relate_table"] . " b','a." . $val["fields"] . "=b." . $val["relate_field"] . "','left')->where(\$where)->order(\$orderby)->paginate(['list_rows'=>\$limit,'page'=>\$page])->toArray();\n";
									} else {
										$str .= "\t\t\t\$res = db('" . $menuInfo["table_name"] . "')->field(\$field)->alias('a')->join('" . $val["relate_table"] . " b','a." . $val["fields"] . "=b." . $val["relate_field"] . "','left')->where(\$where)->order(\$orderby)->paginate(['list_rows'=>\$limit,'page'=>\$page])->toArray();\n";
									}
								} else {
									$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->field(\$field)->order(\$orderby)->paginate(['list_rows'=>\$limit,'page'=>\$page])->toArray();\n";
								}
								$str .= "\t\t}catch(\\Exception \$e){\n";
								if (config("my.error_log_code")) {
									$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
								} else {
									$str .= "\t\t\tabort(500,\$e->getMessage());\n";
								}
								$str .= "\t\t}\n";
								$str .= "\t\treturn ['list'=>\$res['data'],'count'=>\$res['total']];\n";
								$str .= "\t}\n\n\n";
							}
							break;
						case 3:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb()->startTrans();\n\n";
							}
							foreach ($fieldList as $k => $v) {
								if (in_array($v["field"], explode(",", $val["fields"]))) {
									if ($v["type"] == 7) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = strtotime(\$data['" . $v["field"] . "']);\n";
									} elseif ($v["type"] == 5) {
										if (config("my.password_secrect")) {
											$str .= "\t\t\t\$data['" . $v["field"] . "'] = md5(\$data['" . $v["field"] . "'].config('my.password_secrect'));\n";
										} else {
											$str .= "\t\t\t\$data['" . $v["field"] . "'] = md5(\$data['" . $v["field"] . "']);\n";
										}
									} elseif ($v["type"] == 12) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = time();\n";
									} elseif ($v["type"] == 21) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = random(" . $v["default_value"] . ",'all');\n";
									} elseif ($v["type"] == 26) {
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = request()->ip();\n";
									} elseif ($v["type"] == 30) {
										$default_value = !empty($v["default_value"]) ? $v["default_value"] : "000";
										$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = doOrderSn('" . $default_value . "');\n";
									} else {
										$value = \strval($v["default_value"]);
										if ($value || $value == "0") {
											$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = !is_null(\$data['" . $v["field"] . "']) ? \$data['" . $v["field"] . "'] : '" . $value . "';\n";
										}
									}
								}
							}
							$str .= $fieldData;
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::create(\$data);\n";
							if ($val["relate_table"]) {
								$str .= "\t\t\t\$data['" . $menuInfo["pk_id"] . "'] = \$res->" . $menuInfo["pk_id"] . ";\n";
								$str .= "\t\t\tdb('" . $val["relate_table"] . "')->insert(\$data);\n\n";
								$str .= "\t\t\tdb()->commit();\n";
							}
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb()->rollback();\n";
							}
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res->" . $menuInfo["pk_id"] . ";\n";
							$str .= "\t}\n\n\n";
							$rule = "";
							$msg = "";
							$fieldData = "";
							break;
						case 4:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$where,\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb()->startTrans();\n\n";
							}
							foreach ($fieldList as $k => $v) {
								if (in_array($v["field"], explode(",", $val["fields"]))) {
									if (in_array($v["type"], [7, 12])) {
										$fieldData .= "\t\t\t!is_null(\$data['" . $v["field"] . "']) && \$data['" . $v["field"] . "'] = strtotime(\$data['" . $v["field"] . "']);\n";
									} else {
										if ($v["type"] == 25) {
											$fieldData .= "\t\t\t\$data['" . $v["field"] . "'] = time();\n";
										}
									}
								}
							}
							$str .= $fieldData;
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->update(\$data);\n";
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb('" . $val["relate_table"] . "')->where('" . $menuInfo["pk_id"] . "',\$data['" . $menuInfo["pk_id"] . "'])->update(\$data);\n\n";
								$str .= "\t\t\tdb()->commit();\n";
							}
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if ($val["relate_table"]) {
								$str .= "\t\t\tdb()->rollback();\n";
							}
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res;\n";
							$str .= "\t}\n\n\n";
							$rule = "";
							$msg = "";
							$field = "";
							$validate = "";
							$fieldData = "";
							break;
						case 7:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$where,\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->inc('" . $val["fields"] . "',\$data['" . $val["fields"] . "'])->update();\n";
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res;\n";
							$str .= "\t}\n\n\n";
							break;
						case 8:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$where,\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							$str .= "\t\t\t\$info = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->find();\n";
							$str .= "\t\t\tif(\$info->" . $val["fields"] . " < \$data['" . $val["fields"] . "']) throw new ValidateException('操作数据不足');\n";
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->dec('" . $val["fields"] . "',\$data['" . $val["fields"] . "'])->update();\n";
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res;\n";
							$str .= "\t}\n\n\n";
							break;
						case 9:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$where,\$data){\n";
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							if (service\BuildService::checkValidateStatus($val["fields"], $validateFields)) {
								$str .= "\t\t\tvalidate(\\app\\" . $applicationInfo["app_dir"] . "\\validate\\" . getUseName($menuInfo["controller_name"]) . "::class)->scene('" . $val["action_name"] . "')->check(\$data);\n";
							}
							if (config("my.password_secrect")) {
								$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->update(['" . $val["fields"] . "'=>md5(\$data['" . $val["fields"] . "'].config('my.password_secrect'))]);\n";
							} else {
								$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::where(\$where)->update(['" . $val["fields"] . "'=>md5(\$data['" . $val["fields"] . "'])]);\n";
							}
							$str .= "\t\t}catch(ValidateException \$e){\n";
							$str .= "\t\t\tthrow new ValidateException (\$e->getError());\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\treturn \$res;\n";
							$str .= "\t}\n\n\n";
							break;
						case 17:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$data,\$returnField){\n";
							if ($val["remark"]) {
								list($username, $password, $uid) = explode("|", $val["remark"]);
								$str .= "\t\t\$where['" . $username . "'] = \$data['" . $username . "'];\n";
								if (config("my.password_secrect")) {
									$str .= "\t\t\$where['" . $password . "'] = md5(\$data['" . $password . "'].config('my.password_secrect'));\n";
								} else {
									$str .= "\t\t\$where['" . $password . "'] = md5(\$data['" . $password . "']);\n";
								}
							}
							$str .= "\t\ttry{\n";
							foreach ($fieldList as $k => $v) {
								if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 20, 21, 25, 30])) {
									$validateFields[] = $v["field"];
								}
							}
							$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::field(\$returnField)->where(\$where)->find();\n";
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\tif(!\$res){\n";
							$str .= "\t\t\tthrow new ValidateException('请检查用户名或者密码');\n";
							$str .= "\t\t}\n";
							$str .= "\t\treturn checkData(\$res,false);\n";
							$str .= "\t}\n\n\n";
							break;
						case 19:
							$str .= "\t/*\n";
							$str .= " \t* @Description  " . $val["block_name"] . "\n";
							$str .= " \t*/\n";
							$str .= "\tpublic static function " . $val["action_name"] . "(\$mobile,\$returnField){\n";
							$str .= "\t\ttry{\n";
							if ($val["remark"]) {
								$username = explode("|", $val["remark"])[0];
								$str .= "\t\t\t\$where['" . $username . "'] = \$mobile;\n";
								$str .= "\t\t\t\$res = " . getControllerName($menuInfo["controller_name"]) . "::field(\$returnField)->where(\$where)->find();\n";
							}
							$str .= "\t\t}catch(\\Exception \$e){\n";
							if (config("my.error_log_code")) {
								$str .= "\t\t\tabort(config('my.error_log_code'),\$e->getMessage());\n";
							} else {
								$str .= "\t\t\tabort(500,\$e->getMessage());\n";
							}
							$str .= "\t\t}\n";
							$str .= "\t\tif(!\$res){\n";
							$str .= "\t\t\tthrow new ValidateException('请检查手机号');\n";
							$str .= "\t\t}\n";
							$str .= "\t\treturn checkData(\$res,false);\n";
							$str .= "\t}\n\n\n";
							break;
					}
				}
			}
			$rootPath = app()->getRootPath();
			$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/service/" . $menuInfo["controller_name"] . "Service.php";
			filePutContents($str, $filepath, $type = 1);
		}
	}
	public function createModel($actionList, $applicationInfo, $menuInfo)
	{
		$str = "";
		$str = "<?php \n";
		!is_null(config("my.comment.file_comment")) ? config("my.comment.file_comment") : true;
		if (config("my.comment.file_comment")) {
			$str .= "/*\n";
			$str .= " module:\t\t" . $menuInfo["title"] . "模型\n";
			$str .= " create_time:\t" . date("Y-m-d H:i:s") . "\n";
			$str .= " author:\t\t" . config("my.comment.author") . "\n";
			$str .= " contact:\t\t" . config("my.comment.contact") . "\n";
			$str .= "*/\n\n";
		}
		$str .= "namespace app\\" . $applicationInfo["app_dir"] . "\\model" . getDbName($menuInfo["controller_name"]) . ";\n";
		$str .= "use think\\Model;\n";
		$softDeleteAction = db("action")->where(["menu_id" => $menuInfo["menu_id"], "type" => 31])->value("action_name");
		if ($softDeleteAction) {
			$str .= "use think\\model\\concern\\SoftDelete;\n";
		}
		$str .= "\n";
		$str .= "class " . getControllerName($menuInfo["controller_name"]) . " extends Model {\n\n\n";
		if ($softDeleteAction) {
			$delete_field = !is_null(config("my.delete_field")) ? config("my.delete_field") : "delete_time";
			$str .= "\tuse SoftDelete;\n\n";
			$str .= "\tprotected \$deleteTime = '" . $delete_field . "';\n\n";
		}
		if ($menuInfo["connect"]) {
			$str .= "\tprotected \$connection = '" . $menuInfo["connect"] . "';\n\n ";
		}
		$str .= "\tprotected \$pk = '" . $menuInfo["pk_id"] . "';\n\n ";
		$str .= "\tprotected \$name = '" . $menuInfo["table_name"] . "';\n ";
		$rootPath = app()->getRootPath();
		$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/model/" . $menuInfo["controller_name"] . ".php";
		filePutContents($str, $filepath, $type = 1);
	}
	public function createIndexTpl($applicationInfo, $menuInfo, $actionInfo, $fieldList, $actionList)
	{
		$fieldList = $fieldList->toArray();
		$htmlstr = "";
		$htmlstr .= "{extend name='common/_container'} {block name=\"content\"}\n";
		$htmlstr .= "<div class=\"row\">\n";
		$htmlstr .= "\t<div class=\"col-sm-12\">\n";
		$htmlstr .= "\t\t<div class=\"ibox float-e-margins\">\n";
		$list_title_status = !is_null(config("my.list_title_status")) ? config("my.list_title_status") : true;
		if ($list_title_status && $actionInfo["block_name"]) {
			$htmlstr .= "\t\t\t<div class=\"alert alert-dismissable\" style=\"border-left: 5px solid #009688;border-radius: 0 2px 2px 0;background-color: #f2f2f2;\">\n";
			$htmlstr .= "\t\t\t\t" . html_out($actionInfo["block_name"]) . "\n";
			$htmlstr .= "\t\t\t\t<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>\n";
			$htmlstr .= "\t\t\t</div>\n";
		}
		$htmlstr .= "\t\t\t<div class=\"ibox-content\"> \n";
		$htmlstr .= "\t\t\t\t<div class=\"row row-lg\"> \n";
		$htmlstr .= "\t\t\t\t\t<div class=\"col-sm-12\"> \n";
		$htmlstr .= "\t\t\t\t\t\t<div class=\"row\" id=\"searchGroup\">\n";
		foreach ($fieldList as $key => $val) {
			if ($val["search_show"] == 1) {
				if (in_array($val["type"], [1, 2, 3, 4, 6, 7, 12, 13, 17, 20, 21, 23, 27, 28, 29, 30])) {
					switch ($val["type"]) {
						case 7:
							$htmlstr .= service\BuildService::createTimeSearch($val);
							break;
						case 12:
							$htmlstr .= service\BuildService::createTimeSearch($val);
							break;
						case 13:
							$htmlstr .= service\BuildService::createNumSearch($val);
							break;
						case 17:
							$htmlstr .= service\BuildService::createDistaitSearch($val);
							break;
						default:
							$htmlstr .= service\BuildService::createNormaiSearch($val);
					}
					$searchStatus = true;
				}
			}
		}
		$htmlstr .= "\t\t\t\t\t\t\t<!-- search end -->\n";
		$reset_button_status = !is_null(config("my.reset_button_status")) ? config("my.reset_button_status") : true;
		if ($searchStatus) {
			if ($reset_button_status) {
				$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-2\">\n";
			} else {
				$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-1\">\n";
			}
			$htmlstr .= "\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-success \" onclick=\"CodeGoods.search()\" id=\"\">\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t\t<i class=\"fa fa-search\"></i>&nbsp;搜索\n";
			$htmlstr .= "\t\t\t\t\t\t\t\t</button>\n";
			if ($reset_button_status) {
				$htmlstr .= "\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn\" onclick=\"CodeGoods.reset()\" id=\"\">\n";
				$htmlstr .= "\t\t\t\t\t\t\t\t\t<i class=\"glyphicon glyphicon-share-alt\"></i>&nbsp;重置\n";
				$htmlstr .= "\t\t\t\t\t\t\t\t</button>\n";
			}
			$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
		}
		$htmlstr .= "\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t<div class=\"btn-group-sm\" id=\"CodeGoodsTableToolbar\" role=\"group\">\n";
		foreach ($actionList as $key => $val) {
			if ($val["is_view"] && $val["type"] != 1) {
				$buttonGroup[$key] = $val;
			}
			if (!in_array($val["type"], [1, 16, 30])) {
				$scriptGroup[$key] = $val;
			}
			if ($val["button_status"] == 1) {
				$buttonList[$key] = $val;
			}
		}
		foreach ($buttonGroup as $k => $v) {
			$btn_color = !empty($v["lable_color"]) ? $v["lable_color"] : "primary";
			$action_url = empty($v["jump"]) ? $applicationInfo["app_dir"] . "/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] : $applicationInfo["app_dir"] . $v["jump"];
			$htmlstr .= "\t\t\t\t\t\t{if condition=\"in_array('" . $action_url . "',session('" . $applicationInfo["app_dir"] . ".nodes')) || session('" . $applicationInfo["app_dir"] . ".role_id') eq 1\"}\n";
			$htmlstr .= "\t\t\t\t\t\t<button type=\"button\" id=\"" . $v["action_name"] . "\" class=\"btn btn-" . $btn_color . " button-margin\" onclick=\"CodeGoods." . $v["action_name"] . "()\">\n";
			$bs_icon = !empty($v["bs_icon"]) ? $v["bs_icon"] : "fa fa-pencil";
			if (in_array($v["bs_icon"], ["plus", "pencil", "edit", "trash", "plus", "download", "upload"])) {
				$bs_icon = "fa fa-" . $v["bs_icon"];
			}
			$htmlstr .= "\t\t\t\t\t\t<i class=\"" . $bs_icon . "\"></i>&nbsp;" . $v["name"] . "\n";
			$htmlstr .= "\t\t\t\t\t\t</button>\n";
			$htmlstr .= "\t\t\t\t\t\t{/if}\n";
		}
		$htmlstr .= "\t\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t\t\t<table id=\"CodeGoodsTable\" data-mobile-responsive=\"true\" data-click-to-select=\"true\">\n";
		$htmlstr .= "\t\t\t\t\t\t\t<thead><tr><th data-field=\"selectItem\" data-checkbox=\"true\"></th></tr></thead>\n";
		$htmlstr .= "\t\t\t\t\t\t</table>\n";
		$htmlstr .= "\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t</div>\n";
		$htmlstr .= "\t\t</div>\n";
		$htmlstr .= "\t</div>\n";
		$htmlstr .= "</div>\n";
		foreach ($fieldList as $key => $val) {
			if (in_array($val["type"], [29])) {
				$chosen_status = true;
			}
		}
		if ($chosen_status) {
			$htmlstr .= "<link href='__PUBLIC__/static/js/plugins/chosen/chosen.min.css' rel='stylesheet'/>\n";
			$htmlstr .= "<script src='__PUBLIC__/static/js/plugins/chosen/chosen.jquery.js'></script>\n";
			$htmlstr .= "<script>\$(function(){\$('.chosen').chosen({search_contains: true})})</script>\n";
		}
		$htmlstr .= "<script>\n";
		$htmlstr .= "\tvar CodeGoods = {id: \"CodeGoodsTable\",seItem: null,table: null,layerIndex: -1};\n\n";
		$htmlstr .= "\tCodeGoods.initColumn = function () {\n";
		$htmlstr .= " \t\treturn [\n";
		$select_type = $actionInfo["select_type"] == 1 ? "radio" : "checkbox";
		$htmlstr .= " \t\t\t{field: 'selectItem', " . $select_type . ": true},\n";
		$sortidField = db("field")->where(["menu_id" => $menuInfo["menu_id"], "type" => 22])->value("field");
		$sortAction = db("action")->where(["menu_id" => $menuInfo["menu_id"], "type" => 30])->value("action_name");
		if ($sortidField && $sortAction && $actionInfo["type"] == 1) {
			$htmlstr .= " \t\t\t{title: '排序', field: '" . $menuInfo["pk_id"] . "', visible: true, align: 'center', valign: 'middle',formatter: 'CodeGoods.arrowFormatter'},\n";
		}
		$show_fields = $fieldList;
		if ($actionInfo["fields"]) {
			$list_fields = $menuInfo["pk_id"] . "," . $actionInfo["fields"];
			$show_fields = [];
			foreach ($fieldList as $m => $n) {
				if (in_array($n["field"], explode(",", $list_fields))) {
					$show_fields[] = $n;
				}
			}
		}
		if ($actionInfo["relate_table"] && $actionInfo["relate_field"]) {
			$show_fields = $fieldList;
		}
		foreach ($show_fields as $k => $v) {
			if (in_array($v["list_show"], [1, 2])) {
				$show_type = $v["list_show"] == 1 ? "true" : "false";
				if (!empty($v["config"]) || in_array($v["type"], [7, 8, 9, 10, 12, 17, 22, 25, 34])) {
					if ($v["type"] == 17) {
						$htmlstr .= " \t\t\t{title: '" . $v["name"] . "', field: '" . $v["field"] . "', visible: " . $show_type . ", align: '" . $v["align"] . "', valign: 'middle',sortable: true,formatter:CodeGoods." . str_replace("|", "", $v["field"]) . "Formatter },\n";
					} else {
						$htmlstr .= " \t\t\t{title: '" . $v["name"] . "', field: '" . $v["field"] . "', visible: " . $show_type . ", align: '" . $v["align"] . "', valign: 'middle',sortable: true,formatter:CodeGoods." . $v["field"] . "Formatter},\n";
					}
				} else {
					$htmlstr .= " \t\t\t{title: '" . $v["name"] . "', field: '" . $v["field"] . "', visible: " . $show_type . ", align: '" . $v["align"] . "', valign: 'middle',sortable: true},\n";
				}
			}
		}
		$show_fields = "";
		if ($buttonList) {
			$htmlstr .= " \t\t\t{title: '操作', field: '', visible: true, align: '" . $v["align"] . "', valign: 'middle',formatter: 'CodeGoods.buttonFormatter'},\n";
		}
		$htmlstr .= " \t\t];\n";
		$htmlstr .= " \t};\n\n";
		if ($buttonList) {
			$htmlstr .= "\tCodeGoods.buttonFormatter = function(value,row,index) {\n";
			$htmlstr .= "\t\tif(row." . $menuInfo["pk_id"] . "){\n";
			$htmlstr .= "\t\t\tvar str= '';\n";
			foreach ($buttonList as $key => $val) {
				$action_url = empty($val["jump"]) ? $applicationInfo["app_dir"] . "/" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] : $applicationInfo["app_dir"] . $val["jump"];
				$htmlstr .= "\t\t\t{if condition=\"in_array('" . $action_url . "',session('" . $applicationInfo["app_dir"] . ".nodes')) || session('" . $applicationInfo["app_dir"] . ".role_id') eq 1\"}\n";
				$bs_icon = !empty($val["bs_icon"]) ? $val["bs_icon"] : "fa fa-pencil";
				if (in_array($val["bs_icon"], ["plus", "edit", "pencil", "trash", "plus", "download", "upload"])) {
					$bs_icon = "fa fa-" . $val["bs_icon"];
				}
				if (in_array($val["type"], [10, 11]) && $val["fields"]) {
					foreach (explode(",", $val["fields"]) as $m => $n) {
						$hiden_fileld .= "\\''+row." . $n . "+'\\',";
					}
					$htmlstr .= "\t\t\tstr += '<button type=\"button\" class=\"btn btn-" . $val["lable_color"] . " btn-xs\" title=\"" . $val["name"] . "\"  onclick=\"CodeGoods." . $val["action_name"] . "('+row." . $menuInfo["pk_id"] . "+'," . rtrim($hiden_fileld, ",") . ")\"><i class=\"" . $bs_icon . "\"></i>&nbsp;" . $val["name"] . "</button>&nbsp;';\n";
				} else {
					$htmlstr .= "\t\t\tstr += '<button type=\"button\" class=\"btn btn-" . $val["lable_color"] . " btn-xs\" title=\"" . $val["name"] . "\"  onclick=\"CodeGoods." . $val["action_name"] . "('+row." . $menuInfo["pk_id"] . "+')\"><i class=\"" . $bs_icon . "\"></i>&nbsp;" . $val["name"] . "</button>&nbsp;';\n";
				}
				$htmlstr .= "\t\t\t{/if}\n";
				$hiden_fileld = "";
			}
			$htmlstr .= "\t\t\treturn str;\n";
			$htmlstr .= "\t\t}\n";
			$htmlstr .= "\t}\n\n";
		}
		if ($sortidField) {
			$htmlstr .= "\tCodeGoods.arrowFormatter = function(value,row,index) {\n";
			$htmlstr .= "\t\treturn '<i class=\"fa fa-long-arrow-up\" onclick=\"CodeGoods.arrowsort('+row." . $menuInfo["pk_id"] . "+','+row." . $sortidField . "+',1)\" style=\"cursor:pointer;\" title=\"上移\"></i>&nbsp;<i class=\"fa fa-long-arrow-down\" style=\"cursor:pointer;\" onclick=\"CodeGoods.arrowsort('+row." . $menuInfo["pk_id"] . "+','+row." . $sortidField . "+',2)\"  title=\"下移\"></i>';\n";
			$htmlstr .= "\t}\n\n";
			$htmlstr .= "\tCodeGoods.arrowsort = function (pk,sortid,type) {\n";
			$htmlstr .= "\t\tvar ajax = new \$ax(Feng.ctxPath + \"/" . getUrlName($menuInfo["controller_name"]) . "/" . $sortAction . "\", function (data) {\n";
			$htmlstr .= "\t\t\tif ('00' === data.status) {\n";
			$htmlstr .= "\t\t\t\tFeng.success(data.msg);\n";
			$htmlstr .= "\t\t\t\tCodeGoods.table.refresh();\n";
			$htmlstr .= "\t\t\t} else {\n";
			$htmlstr .= "\t\t\t\tFeng.error(data.msg);\n";
			$htmlstr .= "\t\t\t}\n";
			$htmlstr .= "\t\t});\n";
			$htmlstr .= "\t\tajax.set('" . $menuInfo["pk_id"] . "', pk);\n";
			$htmlstr .= "\t\tajax.set('type', type);\n";
			$htmlstr .= "\t\tajax.set('sortid', sortid);\n";
			$htmlstr .= "\t\tajax.start();\n";
			$htmlstr .= "\t}\n\n";
		}
		foreach ($fieldList as $k => $v) {
			if (in_array($v["list_show"], [1, 2])) {
				if (!empty($v["config"]) && ($v["type"] == 1 || $v["type"] == 13)) {
					$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\treturn '<span class=\"label label-" . $v["config"] . "\">'+value+'</span>';\n";
					$htmlstr .= "\t\t}\n";
					$htmlstr .= "\t}\n\n";
				}
				if (in_array($v["type"], [2, 3, 29])) {
					if (!empty($v["config"])) {
						$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
						$htmlstr .= "\t\tif(value !== null){\n";
						$htmlstr .= "\t\t\tvar value = value.toString();\n";
						$htmlstr .= "\t\t\tswitch(value){\n";
						$data = explode(",", $v["config"]);
						if ($data && count($data) > 1) {
							foreach ($data as $key => $val) {
								$valArr = explode("|", $val);
								if ($valArr) {
									$htmlstr .= "\t\t\t\tcase '" . $valArr[1] . "':\n";
									if (!empty($valArr[2])) {
										$htmlstr .= "\t\t\t\t\treturn '<span class=\"label label-" . trim($valArr[2]) . "\">" . $valArr[0] . "</span>';\n";
									} else {
										$htmlstr .= "\t\t\t\t\treturn '" . $valArr[0] . "';\n";
									}
									$htmlstr .= "\t\t\t\tbreak;\n";
								}
							}
						}
						$htmlstr .= "\t\t\t}\n";
						$htmlstr .= "\t\t}\n";
						$htmlstr .= "\t}\n\n";
					}
				}
				if (in_array($v["type"], [4, 27]) && !empty($v["config"]) && empty($v["sql"])) {
					$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\treturn getCheckBoxValue(value,'" . $v["config"] . "');\t\n";
					$htmlstr .= "\t\t}\n";
					$htmlstr .= "\t}\n\n";
				}
				if ($v["type"] == 7 || $v["type"] == 12 || $v["type"] == 25) {
					$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\tif(value){\n";
					$default_time_format = explode("|", $v["default_value"]);
					$time_format = $default_time_format[0];
					if (!$time_format || $v["default_value"] == "null") {
						$time_format = "Y-m-d H:i:s";
					}
					$htmlstr .= "\t\t\treturn formatDateTime(value,'" . $time_format . "');\t\n";
					$htmlstr .= "\t\t}\n";
					$htmlstr .= "\t}\n\n";
				}
				if ($v["type"] == 8) {
					$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\treturn \"<a href=\\\"javascript:void(0)\\\" onclick=\\\"openImg('\"+value+\"')\\\"><img height='30' src=\"+value+\"></a>\";\t\n";
					$htmlstr .= "\t\t}\n";
					$htmlstr .= "\t}\n\n";
				}
				if ($v["type"] == 9) {
					$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\tvar img = JSON.parse(row." . $v["field"] . ".replace(/&quot;/g,'\"'));\t\n";
					$htmlstr .= "\t\t\tvar imgs = '';\t\n";
					$htmlstr .= "\t\t\tfor(var i in img) {\t\n";
					$htmlstr .= "\t\t\t\tif(img[i][\"url\"]){\t\n";
					$htmlstr .= "\t\t\t\t\timgs += \"<a href=\\\"javascript:void(0)\\\" onclick=\\\"openImg('\"+img[i][\"url\"]+\"')\\\"><img height='30' src=\"+img[i][\"url\"]+\"></a>&nbsp;\";\t\n";
					$htmlstr .= "\t\t\t\t}\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t\treturn imgs;\n";
					$htmlstr .= "\t\t}\n";
					$htmlstr .= "\t}\n\n";
				}
				if ($v["type"] == 10) {
					$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\treturn \"<a target='_blank' href=\\\"\"+value+\"\\\">下载附件</a>\";\t\n";
					$htmlstr .= "\t\t}\n";
					$htmlstr .= "\t}\n\n";
				}
				if ($v["type"] == 34) {
					$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\tvar files = row." . $v["field"] . ".split('|');\t\n";
					$htmlstr .= "\t\t\tvar file = '';\t\n";
					$htmlstr .= "\t\t\tfor(var i in files) {\t\n";
					$htmlstr .= "\t\t\t\tif(files[i]){\t\n";
					$htmlstr .= "\t\t\t\t\tfile += \"<a href=\\\"\"+files[i]+\"\\\">附件下载\"+(parseInt(i)+1)+\"</a>&nbsp;&nbsp;\";\t\n";
					$htmlstr .= "\t\t\t\t}\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t\treturn file;\n";
					$htmlstr .= "\t\t}\n";
					$htmlstr .= "\t}\n\n";
				}
				if ($v["type"] == 17) {
					$htmlstr .= "\tCodeGoods." . str_replace("|", "", $v["field"]) . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\t var areaStr = '';\n";
					foreach (explode("|", $v["field"]) as $m => $n) {
						$htmlstr .= "\t\t if(row." . $n . "){\n";
						$htmlstr .= "\t\t \tareaStr += \"-\"+row." . $n . ";\n";
						$htmlstr .= "\t\t }\n";
					}
					$htmlstr .= "\t\tareaStr = areaStr.substr(1);\n";
					$htmlstr .= "\t\treturn areaStr;\n";
					$htmlstr .= "\t}\n\n";
				}
				if ($v["type"] == 22) {
					$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
					$htmlstr .= "\t\treturn '<input type=\"text\" value=\"'+value+'\" onblur=\"CodeGoods.update" . $v["field"] . "('+row." . $menuInfo["pk_id"] . "+',this.value)\" style=\"width:50px; border:1px solid #ddd; text-align:center\">';\n";
					$htmlstr .= "\t}\n\n\n";
					$htmlstr .= "\tCodeGoods.update" . $v["field"] . " = function(pk,value) {\n";
					$htmlstr .= "\t\tvar ajax = new \$ax(Feng.ctxPath + \"/" . getUrlName($menuInfo["controller_name"]) . "/updateExt\", function (data) {\n";
					$htmlstr .= "\t\t\tif ('00' === data.status) {\n";
					$htmlstr .= "\t\t\t} else {\n";
					$htmlstr .= "\t\t\t\tFeng.error(data.msg);\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t});\n";
					$htmlstr .= "\t\tajax.set('" . $menuInfo["pk_id"] . "', pk);\n";
					$htmlstr .= "\t\tajax.set('" . $v["field"] . "', value);\n";
					$htmlstr .= "\t\tajax.start();\n";
					$htmlstr .= "\t}\n\n";
				}
				if ($v["type"] == 23) {
					$listData = explode(",", $v["config"]);
					if (count($listData) == 2) {
						$onData = explode("|", $listData[0]);
						$offData = explode("|", $listData[1]);
						$htmlstr .= "\tCodeGoods." . $v["field"] . "Formatter = function(value,row,index) {\n";
						$htmlstr .= "\t\tif(value !== null){\n";
						$htmlstr .= "\t\t\tif(value == " . $onData[1] . "){\n";
						$htmlstr .= "\t\t\t\treturn '<input class=\"mui-switch mui-switch-animbg " . $v["field"] . "'+row." . $menuInfo["pk_id"] . "+'\" type=\"checkbox\" onclick=\"CodeGoods.update" . $v["field"] . "('+row." . $menuInfo["pk_id"] . "+'," . $offData[1] . ",\\'" . $v["field"] . "\\')\" checked>';\n";
						$htmlstr .= "\t\t\t}else{\n";
						$htmlstr .= "\t\t\t\treturn '<input class=\"mui-switch mui-switch-animbg " . $v["field"] . "'+row." . $menuInfo["pk_id"] . "+'\" type=\"checkbox\" onclick=\"CodeGoods.update" . $v["field"] . "('+row." . $menuInfo["pk_id"] . "+'," . $onData[1] . ",\\'" . $v["field"] . "\\')\">';\n";
						$htmlstr .= "\t\t\t}\n";
						$htmlstr .= "\t\t}\n";
						$htmlstr .= "\t}\n\n\n";
						$htmlstr .= "\tCodeGoods.update" . $v["field"] . " = function(pk,value,field) {\n";
						$htmlstr .= "\t\tvar ajax = new \$ax(Feng.ctxPath + \"/" . getUrlName($menuInfo["controller_name"]) . "/updateExt\", function (data) {\n";
						$htmlstr .= "\t\t\tif ('00' !== data.status) {\n";
						$htmlstr .= "\t\t\t\tFeng.error(data.msg);\n";
						$htmlstr .= "\t\t\t\t\$(\".\"+field+pk).prop(\"checked\",!\$(\".\"+field+pk).prop(\"checked\"));\n";
						$htmlstr .= "\t\t\t}\n";
						$htmlstr .= "\t\t});\n";
						$htmlstr .= "\t\tvar val = \$(\".\"+field+pk).prop(\"checked\") ? 1 : 0;\n";
						$htmlstr .= "\t\tajax.set('" . $menuInfo["pk_id"] . "', pk);\n";
						$htmlstr .= "\t\tajax.set('" . $v["field"] . "', val);\n";
						$htmlstr .= "\t\tajax.start();\n";
						$htmlstr .= "\t}\n\n";
					}
				}
			}
		}
		$htmlstr .= "\tCodeGoods.formParams = function() {\n";
		$htmlstr .= "\t\tvar queryData = {};\n";
		$htmlstr .= "\t\tqueryData['offset'] = 0;\n";
		foreach ($fieldList as $k => $v) {
			if ($v["search_show"] == 1) {
				switch ($v["type"]) {
					case 7:
						$htmlstr .= "\t\tqueryData['" . $v["field"] . "_start'] = \$(\"#" . $v["field"] . "\").val().split(\" - \")[0];\n";
						$htmlstr .= "\t\tqueryData['" . $v["field"] . "_end'] = \$(\"#" . $v["field"] . "\").val().split(\" - \")[1];\n";
						break;
					case 12:
						$htmlstr .= "\t\tqueryData['" . $v["field"] . "_start'] = \$(\"#" . $v["field"] . "\").val().split(\" - \")[0];\n";
						$htmlstr .= "\t\tqueryData['" . $v["field"] . "_end'] = \$(\"#" . $v["field"] . "\").val().split(\" - \")[1];\n";
						break;
					case 13:
						$htmlstr .= "\t\tqueryData['" . $v["field"] . "_start'] = \$(\"#" . $v["field"] . "_start\").val();\n";
						$htmlstr .= "\t\tqueryData['" . $v["field"] . "_end'] = \$(\"#" . $v["field"] . "_end\").val();\n";
						break;
					case 17:
						foreach (explode("|", $v["field"]) as $m => $n) {
							$htmlstr .= "\t\tqueryData['" . $n . "'] = \$(\"#" . $n . "\").val();\n";
						}
						break;
					default:
						if ($v["field"] == "name") {
							$v["field"] = "name_s";
						}
						$htmlstr .= "\t\tqueryData['" . $v["field"] . "'] = \$(\"#" . $v["field"] . "\").val();\n";
				}
			}
		}
		$htmlstr .= "\t\treturn queryData;\n";
		$htmlstr .= "\t}\n\n";
		$htmlstr .= "\tCodeGoods.check = function () {\n";
		$htmlstr .= "\t\tvar selected = \$('#' + this.id).bootstrapTable('getSelections');\n";
		$htmlstr .= "\t\tif(selected.length == 0){\n";
		$htmlstr .= "\t\t\tFeng.info(\"请先选中表格中的某一记录！\");\n";
		$htmlstr .= "\t\t\treturn false;\n";
		$htmlstr .= "\t\t}else{\n";
		if ($select_type == "checkbox") {
			$htmlstr .= "\t\t\tCodeGoods.seItem = selected;\n";
		} else {
			$htmlstr .= "\t\t\tCodeGoods.seItem = selected[0];\n";
		}
		$htmlstr .= "\t\t\treturn true;\n";
		$htmlstr .= "\t\t}\n";
		$htmlstr .= "\t};\n\n";
		foreach ($scriptGroup as $k => $v) {
			if (in_array($v["type"], [10, 11]) && $v["fields"]) {
				$htmlstr .= "\tCodeGoods." . $v["action_name"] . " = function (value," . $v["fields"] . ") {\n";
			} else {
				$htmlstr .= "\tCodeGoods." . $v["action_name"] . " = function (value) {\n";
			}
			switch ($v["type"]) {
				case 3:
					list($width, $height) = explode("|", $v["remark"]);
					$htmlstr .= "\t\tvar url = location.search;\n";
					$htmlstr .= "\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "'+url});\n";
					$htmlstr .= "\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\tif(!IsPC()){layer.full(index)}\n";
					break;
				case 4:
					list($width, $height) = explode("|", $v["remark"]);
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "?" . $menuInfo["pk_id"] . "='+value});\n";
					$htmlstr .= "\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t}else{\n";
					$htmlstr .= "\t\t\tif (this.check()) {\n";
					if ($select_type == "checkbox") {
						$htmlstr .= "\t\t\t\tvar idx = '';\n";
						$htmlstr .= "\t\t\t\t\$.each(CodeGoods.seItem, function() {\n";
						$htmlstr .= "\t\t\t\t\tidx += ',' + this." . $menuInfo["pk_id"] . ";\n";
						$htmlstr .= "\t\t\t\t});\n";
						$htmlstr .= "\t\t\t\tidx = idx.substr(1);\n";
						$htmlstr .= "\t\t\t\tif(idx.indexOf(\",\") !== -1){\n";
						$htmlstr .= "\t\t\t\t\tFeng.info(\"请选择单条数据！\");\n";
						$htmlstr .= "\t\t\t\t\treturn false;\n";
						$htmlstr .= "\t\t\t\t}\n";
					} else {
						$htmlstr .= "\t\t\t\tvar idx = this.seItem." . $menuInfo["pk_id"] . ";\n";
					}
					$htmlstr .= "\t\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "?" . $menuInfo["pk_id"] . "='+idx});\n";
					$htmlstr .= "\t\t\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t}\n";
					break;
				case in_array($v["type"], [5, 6, 31, 33, 34]):
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\tFeng.confirm(\"是否" . $v["name"] . "选中项？\", function () {\n";
					$htmlstr .= "\t\t\t\tvar ajax = new \$ax(Feng.ctxPath + \"/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "\", function (data) {\n";
					$htmlstr .= "\t\t\t\t\tif ('00' === data.status) {\n";
					$htmlstr .= "\t\t\t\t\t\tFeng.success(data.msg);\n";
					$htmlstr .= "\t\t\t\t\t\tCodeGoods.table.refresh();\n";
					$htmlstr .= "\t\t\t\t\t} else {\n";
					$htmlstr .= "\t\t\t\t\t\tFeng.error(data.msg);\n";
					$htmlstr .= "\t\t\t\t\t}\n";
					$htmlstr .= "\t\t\t\t});\n";
					$htmlstr .= "\t\t\t\tajax.set('" . $menuInfo["pk_id"] . "', value);\n";
					$htmlstr .= "\t\t\t\tajax.start();\n";
					$htmlstr .= "\t\t\t});\n";
					$htmlstr .= "\t\t}else{\n";
					$htmlstr .= "\t\t\tif (this.check()) {\n";
					if ($select_type == "checkbox") {
						$htmlstr .= "\t\t\t\tvar idx = '';\n";
						$htmlstr .= "\t\t\t\t\$.each(CodeGoods.seItem, function() {\n";
						$htmlstr .= "\t\t\t\t\tidx += ',' + this." . $menuInfo["pk_id"] . ";\n";
						$htmlstr .= "\t\t\t\t});\n";
						$htmlstr .= "\t\t\t\tidx = idx.substr(1);\n";
					} else {
						$htmlstr .= "\t\t\t\tvar idx = this.seItem." . $menuInfo["pk_id"] . ";\n";
					}
					$htmlstr .= "\t\t\t\tFeng.confirm(\"是否" . $v["name"] . "选中项？\", function () {\n";
					$htmlstr .= "\t\t\t\t\tvar ajax = new \$ax(Feng.ctxPath + \"/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "\", function (data) {\n";
					$htmlstr .= "\t\t\t\t\t\tif ('00' === data.status) {\n";
					$htmlstr .= "\t\t\t\t\t\t\tFeng.success(data.msg,1000);\n";
					$htmlstr .= "\t\t\t\t\t\t\tCodeGoods.table.refresh();\n";
					$htmlstr .= "\t\t\t\t\t\t} else {\n";
					$htmlstr .= "\t\t\t\t\t\t\tFeng.error(data.msg,1000);\n";
					$htmlstr .= "\t\t\t\t\t\t}\n";
					$htmlstr .= "\t\t\t\t\t});\n";
					$htmlstr .= "\t\t\t\t\tajax.set('" . $menuInfo["pk_id"] . "', idx);\n";
					$htmlstr .= "\t\t\t\t\tajax.start();\n";
					$htmlstr .= "\t\t\t\t});\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t}\n";
					break;
				case in_array($v["type"], [7, 8, 9]):
					list($width, $height) = explode("|", $v["remark"]);
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "?" . $menuInfo["pk_id"] . "='+value});\n";
					$htmlstr .= "\t\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t}else{\n";
					$htmlstr .= "\t\t\tif (this.check()) {\n";
					if ($select_type == "checkbox") {
						$htmlstr .= "\t\t\t\tvar idx = '';\n";
						$htmlstr .= "\t\t\t\t\$.each(CodeGoods.seItem, function() {\n";
						$htmlstr .= "\t\t\t\t\tidx += ',' + this." . $menuInfo["pk_id"] . ";\n";
						$htmlstr .= "\t\t\t\t});\n";
						$htmlstr .= "\t\t\t\tidx = idx.substr(1);\n";
					} else {
						$htmlstr .= "\t\t\t\tvar idx = this.seItem." . $menuInfo["pk_id"] . ";\n";
					}
					$htmlstr .= "\t\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "?" . $menuInfo["pk_id"] . "='+idx});\n";
					$htmlstr .= "\t\t\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t}\n";
					break;
				case 10:
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\tvar queryData = {};\n";
					$htmlstr .= "\t\t\tqueryData['" . $menuInfo["pk_id"] . "'] = value;\n";
					foreach (explode(",", $v["fields"]) as $m => $n) {
						if ($n) {
							$htmlstr .= "\t\t\tqueryData['" . $n . "'] = " . $n . ";\n";
						}
					}
					$htmlstr .= "\t\t\tlocation.href= Feng.ctxPath +'" . $v["jump"] . "?'+Feng.parseParam(queryData);\n";
					$htmlstr .= "\t\t}else{\n";
					if ($select_type == "checkbox") {
						$htmlstr .= "\t\t\tif (this.check()) {\n";
						$htmlstr .= "\t\t\t\tvar idx = '';\n";
						foreach (explode(",", $v["fields"]) as $m => $n) {
							if ($n) {
								$htmlstr .= "\t\t\t\tvar " . $n . " = '';\n";
							}
						}
						$htmlstr .= "\t\t\t\t\$.each(CodeGoods.seItem, function() {\n";
						$htmlstr .= "\t\t\t\t\tidx += ',' + this." . $menuInfo["pk_id"] . ";\n";
						foreach (explode(",", $v["fields"]) as $m => $n) {
							if ($n) {
								$htmlstr .= "\t\t\t\t\t" . $n . " += ',' + this." . $n . ";\n";
							}
						}
						$htmlstr .= "\t\t\t\t});\n";
						$htmlstr .= "\t\t\t\tidx = idx.substr(1);\n";
						foreach (explode(",", $v["fields"]) as $m => $n) {
							if ($n) {
								$htmlstr .= "\t\t\t\t" . $n . " = " . $n . ".substr(1);\n";
							}
						}
						$htmlstr .= "\t\t\t\tif(idx.indexOf(\",\") !== -1){\n";
						$htmlstr .= "\t\t\t\t\tFeng.info(\"请选择单条数据！\");\n";
						$htmlstr .= "\t\t\t\t\treturn false;\n";
						$htmlstr .= "\t\t\t\t}\n";
					} else {
						$htmlstr .= "\t\t\t\tvar idx = this.seItem." . $menuInfo["pk_id"] . ";\n";
						foreach (explode(",", $v["fields"]) as $m => $n) {
							if ($n) {
								$htmlstr .= "\t\t\t\tvar " . $n . " = this.seItem." . $n . ";\n";
							}
						}
					}
					$htmlstr .= "\t\t\t\tvar queryData = {};\n";
					$htmlstr .= "\t\t\t\tqueryData['" . $menuInfo["pk_id"] . "'] = idx;\n";
					foreach (explode(",", $v["fields"]) as $m => $n) {
						if ($n) {
							$htmlstr .= "\t\t\t\tqueryData['" . $n . "'] = " . $n . ";\n";
						}
					}
					$htmlstr .= "\t\t\t\tlocation.href= Feng.ctxPath +'" . $v["jump"] . "?'+Feng.parseParam(queryData);\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t}\n";
					break;
				case 11:
					list($width, $height) = explode("|", $v["remark"]);
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\tvar queryData = {};\n";
					$htmlstr .= "\t\t\tqueryData['" . $menuInfo["pk_id"] . "'] = value;\n";
					foreach (explode(",", $v["fields"]) as $m => $n) {
						if ($n) {
							$htmlstr .= "\t\t\tqueryData['" . $n . "'] = " . $n . ";\n";
						}
					}
					$htmlstr .= "\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '" . $v["jump"] . "?'+Feng.parseParam(queryData)});\n";
					$htmlstr .= "\t\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t}else{\n";
					$htmlstr .= "\t\t\tif (this.check()) {\n";
					if ($select_type == "checkbox") {
						$htmlstr .= "\t\t\t\tvar idx = '';\n";
						foreach (explode(",", $v["fields"]) as $m => $n) {
							if ($n) {
								$htmlstr .= "\t\t\t\tvar " . $n . " = '';\n";
							}
						}
						$htmlstr .= "\t\t\t\t\$.each(CodeGoods.seItem, function() {\n";
						$htmlstr .= "\t\t\t\t\tidx += ',' + this." . $menuInfo["pk_id"] . ";\n";
						foreach (explode(",", $v["fields"]) as $m => $n) {
							if ($n) {
								$htmlstr .= "\t\t\t\t\t" . $n . " += ',' + this." . $n . ";\n";
							}
						}
						$htmlstr .= "\t\t\t\t});\n";
						$htmlstr .= "\t\t\t\tidx = idx.substr(1);\n";
						foreach (explode(",", $v["fields"]) as $m => $n) {
							if ($n) {
								$htmlstr .= "\t\t\t\t" . $n . " = " . $n . ".substr(1);\n";
							}
						}
						$htmlstr .= "\t\t\t\tif(idx.indexOf(\",\") !== -1){\n";
						$htmlstr .= "\t\t\t\t\tFeng.info(\"请选择单条数据！\");\n";
						$htmlstr .= "\t\t\t\t\treturn false;\n";
						$htmlstr .= "\t\t\t\t}\n";
					} else {
						$htmlstr .= "\t\t\t\tvar idx = this.seItem." . $menuInfo["pk_id"] . ";\n";
						foreach (explode(",", $v["fields"]) as $m => $n) {
							if ($n) {
								$htmlstr .= "\t\t\t\tvar " . $n . " = this.seItem." . $n . ";\n";
							}
						}
					}
					$htmlstr .= "\t\t\t\tvar queryData = {};\n";
					$htmlstr .= "\t\t\t\tqueryData['" . $menuInfo["pk_id"] . "'] = idx;\n";
					foreach (explode(",", $v["fields"]) as $m => $n) {
						if ($n) {
							$htmlstr .= "\t\t\t\tqueryData['" . $n . "'] = " . $n . ";\n";
						}
					}
					$htmlstr .= "\t\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '" . $v["jump"] . "?'+Feng.parseParam(queryData)});\n";
					$htmlstr .= "\t\t\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t}\n";
					break;
				case 12:
					$htmlstr .= "\t\tvar select_id = '';\n";
					$htmlstr .= "\t\tif (this.check()){\n";
					$htmlstr .= "\t\t\t\$.each(CodeGoods.seItem, function() {\n";
					$htmlstr .= "\t\t\t\tselect_id += ',' + this." . $menuInfo["pk_id"] . ";\n";
					$htmlstr .= "\t\t\t});\n";
					$htmlstr .= "\t\t}\n";
					$htmlstr .= "\t\tselect_id = select_id.substr(1);\n";
					$htmlstr .= "\t\tFeng.confirm(\"是否确定导出记录?\", function() {\n";
					$htmlstr .= "\t\t\tvar index = layer.msg('正在导出下载，请耐心等待...', {\n";
					$htmlstr .= "\t\t\t\ttime : 3600000,\n";
					$htmlstr .= "\t\t\t\ticon : 16,\n";
					$htmlstr .= "\t\t\t\tshade : 0.01\n";
					$htmlstr .= "\t\t\t});\n";
					$htmlstr .= "\t\t\tvar idx =[];\n";
					$htmlstr .= "\t\t\t\$(\"li input:checked\").each(function(){\n";
					$htmlstr .= "\t\t\t\tidx.push(\$(this).attr('data-field'));\n";
					$htmlstr .= "\t\t\t});\n";
					$htmlstr .= "\t\t\tvar queryData = CodeGoods.formParams();\n";
					$htmlstr .= "\t\t\twindow.location.href = Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "?action_id=" . $menuInfo["menu_id"] . "&' + Feng.parseParam(queryData) + '&' +Feng.parseParam(idx) + '&" . $menuInfo["pk_id"] . "=' + select_id;\n";
					$htmlstr .= "\t\t\tsetTimeout(function() {\n";
					$htmlstr .= "\t\t\t\tlayer.close(index)\n";
					$htmlstr .= "\t\t\t}, 1000);\n";
					$htmlstr .= "\t\t});\n";
					break;
				case 13:
					$htmlstr .= "\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['500px', '300px'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "'});\n";
					$htmlstr .= "\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\tif(!IsPC()){layer.full(index)}\n";
					break;
				case 14:
					list($width, $height) = explode("|", $v["remark"]);
					$htmlstr .= "\t\tif (this.check()) {\n";
					if ($select_type == "checkbox") {
						$htmlstr .= "\t\t\tvar idx = '';\n";
						$htmlstr .= "\t\t\t\$.each(CodeGoods.seItem, function() {\n";
						$htmlstr .= "\t\t\t\tidx += ',' + this." . $menuInfo["pk_id"] . ";\n";
						$htmlstr .= "\t\t\t});\n";
						$htmlstr .= "\t\t\tidx = idx.substr(1);\n";
					} else {
						$htmlstr .= "\t\t\tvar idx = this.seItem." . $menuInfo["pk_id"] . ";\n";
					}
					$htmlstr .= "\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "?" . $menuInfo["pk_id"] . "='+idx});\n";
					$htmlstr .= "\t\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t}\n";
					break;
				case 15:
					list($width, $height) = explode("|", $v["remark"]);
					$htmlstr .= "\t\tif(value){\n";
					$htmlstr .= "\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "?" . $menuInfo["pk_id"] . "='+value});\n";
					$htmlstr .= "\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t}else{\n";
					$htmlstr .= "\t\t\tif (this.check()) {\n";
					if ($select_type == "checkbox") {
						$htmlstr .= "\t\t\t\tvar idx = '';\n";
						$htmlstr .= "\t\t\t\t\$.each(CodeGoods.seItem, function() {\n";
						$htmlstr .= "\t\t\t\t\tidx += ',' + this." . $menuInfo["pk_id"] . ";\n";
						$htmlstr .= "\t\t\t\t});\n";
						$htmlstr .= "\t\t\t\tidx = idx.substr(1);\n";
						$htmlstr .= "\t\t\t\tif(idx.indexOf(\",\") !== -1){\n";
						$htmlstr .= "\t\t\t\t\tFeng.info(\"请选择单条数据！\");\n";
						$htmlstr .= "\t\t\t\t\treturn false;\n";
						$htmlstr .= "\t\t\t\t}\n";
					} else {
						$htmlstr .= "\t\t\t\tvar idx = this.seItem." . $menuInfo["pk_id"] . ";\n";
					}
					$htmlstr .= "\t\t\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['" . $width . "', '" . $height . "'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "?" . $menuInfo["pk_id"] . "='+idx});\n";
					$htmlstr .= "\t\t\t\tthis.layerIndex = index;\n";
					$htmlstr .= "\t\t\t\tif(!IsPC()){layer.full(index)}\n";
					$htmlstr .= "\t\t\t}\n";
					$htmlstr .= "\t\t}\n";
					break;
				case 32:
					$htmlstr .= "\t\tvar index = layer.open({type: 2,title: '" . $v["name"] . "',area: ['95%', '95%'],fix: false, maxmin: true,content: Feng.ctxPath + '/" . getUrlName($menuInfo["controller_name"]) . "/" . $v["action_name"] . "'});\n";
					$htmlstr .= "\t\tthis.layerIndex = index;\n";
					break;
			}
			$htmlstr .= "\t}\n\n\n";
		}
		$htmlstr .= "\tCodeGoods.search = function() {\n";
		$htmlstr .= "\t\tCodeGoods.table.refresh({query : CodeGoods.formParams()});\n";
		$htmlstr .= "\t};\n\n";
		if ($reset_button_status) {
			$htmlstr .= "\tCodeGoods.reset = function() {\n";
			$htmlstr .= "\t\t\$(\"#searchGroup input,select\").val('');\n";
			$htmlstr .= "\t\tCodeGoods.table.refresh({query : CodeGoods.formParams()});\n";
			$htmlstr .= "\t};\n\n";
		}
		$listWhere["menu_id"] = $menuInfo["menu_id"];
		$listWhere["type"] = 1;
		$fieldInfo = model\Action::where($listWhere)->find();
		$pagesize = !empty($fieldInfo["pagesize"]) ? $fieldInfo["pagesize"] : 20;
		$htmlstr .= "\t\$(function() {\n";
		$htmlstr .= "\t\tvar defaultColunms = CodeGoods.initColumn();\n";
		$htmlstr .= "\t\tvar url = location.search;\n";
		$htmlstr .= "\t\tvar table = new BSTable(CodeGoods.id, Feng.ctxPath+\"/" . getUrlName($menuInfo["controller_name"]) . "/" . $actionInfo["action_name"] . "\"+url,defaultColunms," . $pagesize . ");\n";
		$htmlstr .= "\t\ttable.setPaginationType(\"server\");\n";
		$htmlstr .= "\t\ttable.setQueryParams(CodeGoods.formParams());\n";
		$htmlstr .= "\t\tCodeGoods.table = table.init();\n";
		$htmlstr .= "\t});\n";
		foreach ($fieldList as $key => $val) {
			if (in_array($val["type"], [7, 12]) && $val["search_show"]) {
				$dateList = service\FieldSetService::dateList();
				$default_value = explode("|", $val["default_value"]);
				$time_format = $dateList[$default_value[0]];
				if (!$time_format || $val["default_value"] == "null") {
					$time_format = "datetime";
				}
				$htmlstr .= "\tlaydate.render({elem: '#" . $val["field"] . "',type: '" . $time_format . "',range:true,\n";
				$htmlstr .= "\t\tready: function(date){\n";
				$htmlstr .= "\t\t\t\$(\".layui-laydate-footer [lay-type='datetime'].laydate-btns-time\").click();\n";
				$htmlstr .= "\t\t\t\$(\".laydate-main-list-1 .layui-laydate-content li ol li:last-child\").click();\n";
				$htmlstr .= "\t\t\t\$(\".layui-laydate-footer [lay-type='date'].laydate-btns-time\").click();\n";
				$htmlstr .= "\t\t}\n";
				$htmlstr .= "\t});\n";
			}
		}
		$htmlstr .= "</script>\n";
		$htmlstr .= "{/block}";
		$rootPath = app()->getRootPath();
		$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/view/" . getViewName($menuInfo["controller_name"]) . "/" . $actionInfo["action_name"] . ".html";
		filePutContents($htmlstr, $filepath, $type = 2);
	}
	public static function createInfoTpl($applicationInfo, $menuInfo, $actionInfo, $fieldList)
	{
		$tabList = service\FieldSetService::tabList($menuInfo["menu_id"]);
		if ($tabList) {
			$htmlstr .= self::tabForm($fieldList, $actionInfo, $tabList, $menuInfo, $applicationInfo);
		} else {
			$htmlstr .= self::normalForm($fieldList, $actionInfo, $menuInfo, $applicationInfo);
		}
		$htmlstr .= "\t\t\t<div class=\"hr-line-dashed\"></div>\n";
		$htmlstr .= "\t\t\t<div class=\"row btn-group-m-t\">\n";
		$sizeArr = explode("|", $actionInfo["remark"]);
		preg_match_all("/\\d+/", $sizeArr[0], $res);
		$width = $res[0][0];
		$htmlstr .= "\t\t\t\t<div class=\"col-sm-9 col-sm-offset-1\">\n";
		$htmlstr .= "\t\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" onclick=\"CodeInfoDlg." . $actionInfo["action_name"] . "()\" id=\"ensure\">\n";
		$htmlstr .= "\t\t\t\t\t\t<i class=\"fa fa-check\"></i>&nbsp;确认提交\n";
		$htmlstr .= "\t\t\t\t\t</button>\n";
		$htmlstr .= "\t\t\t\t\t<button type=\"button\" class=\"btn btn-danger\" onclick=\"CodeInfoDlg.close()\" id=\"cancel\">\n";
		$htmlstr .= "\t\t\t\t\t\t<i class=\"fa fa-eraser\"></i>&nbsp;取消\n";
		$htmlstr .= "\t\t\t\t\t</button>\n";
		$htmlstr .= "\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t</div>\n";
		$htmlstr .= "\t\t</div>\n";
		$htmlstr .= "\t</div>\n";
		$htmlstr .= "</div>\n";
		$htmlstr .= "<script src=\"__PUBLIC__/static/js/upload.js\" charset=\"utf-8\"></script>\n";
		$htmlstr .= "<script src=\"__PUBLIC__/static/js/plugins/layui/layui.js\" charset=\"utf-8\"></script>\n";
		foreach ($fieldList as $key => $val) {
			if (in_array($val["type"], [27, 29]) && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
				$chosen_status = true;
			}
			if ($val["type"] == 28 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
				$tag_status = true;
			}
			if ($val["type"] == 9 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
				$images_status = true;
			}
			if ($val["type"] == 32 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
				$jzd = true;
			}
		}
		if ($chosen_status) {
			$htmlstr .= "<link href='__PUBLIC__/static/js/plugins/chosen/chosen.min.css' rel='stylesheet'/>\n";
			$htmlstr .= "<script src='__PUBLIC__/static/js/plugins/chosen/chosen.jquery.js'></script>\n";
		}
		if ($tag_status) {
			$htmlstr .= "<link rel='stylesheet' href='__PUBLIC__/static/js/plugins/tagsinput/tagsinput.css'>\n";
			$htmlstr .= "<script src='__PUBLIC__/static/js/plugins/tagsinput/tagsinput.min.js'></script>\n";
		}
		if ($images_status || $jzd) {
			$htmlstr .= "<script src='__PUBLIC__/static/js/plugins/paixu/jquery-migrate-1.1.1.js'></script>\n";
			$htmlstr .= "<script src='__PUBLIC__/static/js/plugins/paixu/jquery.dragsort-0.5.1.min.js'></script>\n";
		}
		$htmlstr .= "<script>\n";
		if ($images_status || $jzd) {
			$htmlstr .= "\$(function(){\n";
			foreach ($fieldList as $key => $val) {
				if ($val["type"] == 32 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "\t\$(\"." . $val["field"] . "\").dragsort({dragSelector: \".move\",dragBetween: true ,dragEnd:function(){}});\n";
				}
				if ($val["type"] == 9 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "\t\$(\".filelist\").dragsort({dragSelector: \"img\",dragBetween: true ,dragEnd:function(){}});\n";
				}
			}
			$htmlstr .= "});\n";
		}
		$htmlstr .= "layui.use(['form'],function(){});\n";
		if ($tabList) {
			$htmlstr .= "layui.use('element', function(){\n";
			$htmlstr .= "\tvar element = layui.element;\n";
			$htmlstr .= "\telement.on('tab(test)', function(elem){\n";
			$firstMenuName = $tabList[0];
			unset($tabList[0]);
			foreach ($fieldList as $key => $val) {
				if ($val["type"] == 8 && $val["is_post"] == 1 && in_array($val["tab_menu_name"], $tabList) && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "\t\tuploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',false,'','{:getUploadServerUrl()}');\n";
				}
				if ($val["type"] == 10 && $val["is_post"] == 1 && in_array($val["tab_menu_name"], $tabList) && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "\t\tuploader('" . $val["field"] . "_upload','" . $val["field"] . "','file',false,'','{:getUploadServerUrl()}');\n";
				}
				if ($val["type"] == 27 && $val["is_post"] == 1 && in_array($val["tab_menu_name"], $tabList) && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "\t\t\$(\".chosen-container\").css('width','100%');\n";
				}
				if ($val["type"] == 29 && $val["is_post"] == 1 && in_array($val["tab_menu_name"], $tabList) && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "\t\t\$(\".chosen-container\").css('width','100%');\n";
				}
				if ($val["type"] == 34 && $val["is_post"] == 1 && in_array($val["tab_menu_name"], $tabList) && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "\t\tuploader('" . $val["field"] . "_upload','" . $val["field"] . "','file',true,'','{:getUploadServerUrl()}');\n";
				}
			}
			$htmlstr .= "\t});\n";
			$htmlstr .= "});\n";
		}
		if ($fieldList) {
			foreach ($fieldList as $key => $val) {
				if ($val["type"] == 8 && $val["is_post"] == 1 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					if ($menuInfo["upload_config_id"]) {
						$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',false,'','{:getUploadServerUrl(" . $menuInfo["upload_config_id"] . ")}');\n";
					} else {
						$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',false,'','{:getUploadServerUrl()}');\n";
					}
				}
				if ($val["type"] == 9 && $val["is_post"] == 1 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					if ($menuInfo["upload_config_id"]) {
						$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',true,'{\$info." . $val["field"] . "}','{:getUploadServerUrl(" . $menuInfo["upload_config_id"] . ")}');\n";
					} else {
						$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',true,'{\$info." . $val["field"] . "}','{:getUploadServerUrl()}');\n";
					}
					$htmlstr .= "setUploadButton('" . $val["field"] . "_upload');\n";
				}
				if ($val["type"] == 10 && $val["is_post"] == 1 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','file',false,'','{:getUploadServerUrl()}');\n";
				}
				if ($val["type"] == 34 && $val["is_post"] == 1 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','file',true,'{\$info." . $val["field"] . "}','{:getUploadServerUrl()}');\n";
				}
			}
		}
		if ($chosen_status) {
			$htmlstr .= "\$(function(){\$('.chosen').chosen({search_contains: true})})\n";
		}
		foreach ($fieldList as $key => $val) {
			if (in_array($val["type"], [7, 12, 25, 31]) && $val["is_post"] && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
				$dateList = service\FieldSetService::dateList();
				$default_value = explode("|", $val["default_value"]);
				$time_format = $dateList[$default_value[0]];
				if (!$time_format || $val["default_value"] == "null") {
					$time_format = "datetime";
				}
				if ($val["type"] == 31) {
					$htmlstr .= "laydate.render({elem: '#" . $val["field"] . "',type: '" . $time_format . "',range:true,trigger:'click'});\n";
				} else {
					$htmlstr .= "laydate.render({elem: '#" . $val["field"] . "',type: '" . $time_format . "',trigger:'click'});\n";
				}
			}
		}
		$htmlstr .= "var CodeInfoDlg = {\n";
		$htmlstr .= "\tCodeInfoData: {},\n";
		$htmlstr .= "\tvalidateFields: {\n";
		foreach ($fieldList as $key => $val) {
			$val = checkData($val);
			if ((!empty($val["validate"]) || !empty($val["rule"])) && $val["type"] != 17 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
				$htmlstr .= "\t\t" . $val["field"] . ": {\n";
				$htmlstr .= "\t\t\tvalidators: {\n";
				if (in_array("notEmpty", explode(",", $val["validate"]))) {
					$htmlstr .= "\t\t\t\tnotEmpty: {\n";
					$htmlstr .= "\t\t\t\t\tmessage: '" . $val["name"] . "不能为空'\n";
					$htmlstr .= "\t \t\t\t},\n";
				}
				if (!empty($val["rule"])) {
					$htmlstr .= "\t\t\t\tregexp: {\n";
					$htmlstr .= "\t\t\t\t\tregexp: " . $val["rule"] . ",\n";
					$htmlstr .= "\t\t\t\t\tmessage: '" . $val["message"] . "'\n";
					$htmlstr .= "\t \t\t\t},\n";
				}
				$htmlstr .= "\t \t\t}\n";
				$htmlstr .= "\t \t},\n";
			}
		}
		$htmlstr .= "\t }\n";
		$htmlstr .= "}\n\n";
		$htmlstr .= "CodeInfoDlg.collectData = function () {\n";
		$htmlstr .= "\tthis";
		$htmlstr .= ".set('" . $menuInfo["pk_id"] . "')";
		foreach ($fieldList as $key => $val) {
			if (in_array($val["field"], explode(",", $actionInfo["fields"]))) {
				if (!in_array($val["type"], [3, 4, 9, 16, 17, 23, 32, 33, 34])) {
					$htmlstr .= ".set('" . $val["field"] . "')";
				}
				if ($val["type"] == 17 && !empty($val["field"])) {
					foreach (explode("|", $val["field"]) as $k => $v) {
						$htmlstr .= ".set('" . $v . "')";
					}
				}
			}
		}
		$htmlstr .= ";\n";
		$htmlstr .= "};\n\n";
		if (in_array($actionInfo["type"], [3, 4, 14])) {
			$htmlstr .= "CodeInfoDlg." . $actionInfo["action_name"] . " = function () {\n";
			$htmlstr .= "\t this.clearData();\n";
			$htmlstr .= "\t this.collectData();\n";
			$htmlstr .= "\t if (!this.validate()) {\n";
			$htmlstr .= "\t \treturn;\n";
			$htmlstr .= "\t }\n";
			foreach ($fieldList as $k => $v) {
				if (in_array($v["field"], explode(",", $actionInfo["fields"]))) {
					if ($v["type"] == 3 || $v["type"] == 23) {
						$htmlstr .= "\t var " . $v["field"] . " = \$(\"input[name = '" . $v["field"] . "']:checked\").val();\n";
					}
					if ($v["type"] == 4) {
						$htmlstr .= "\t var " . $v["field"] . " = '';\n";
						$htmlstr .= "\t \$('input[name=\"" . $v["field"] . "\"]:checked').each(function(){ \n";
						$htmlstr .= "\t \t" . $v["field"] . " += ',' + \$(this).val(); \n";
						$htmlstr .= "\t }); \n";
						$htmlstr .= "\t  " . $v["field"] . " = " . $v["field"] . ".substr(1); \n";
					}
					if ($v["type"] == 9) {
						$htmlstr .= "\t var " . $v["field"] . " = {};\n";
						$htmlstr .= "\t \$(\"." . $v["field"] . " li\").each(function() {\n";
						$htmlstr .= "\t\tif(\$(this).find('img').attr('src')){\n";
						$htmlstr .= "\t \t\t" . $v["field"] . "[\$(this).index()] = {'url':\$(this).find('img').attr('src'),'title':\$(this).find('input').val()};\n";
						$htmlstr .= "\t\t}\n";
						$htmlstr .= "\t });\n";
					}
					if ($v["type"] == 16) {
						$htmlstr .= "\t var " . $v["field"] . " = UE.getEditor('" . $v["field"] . "').getContent();\n";
					}
					if ($v["type"] == 32) {
						$htmlstr .= "\t var " . $v["field"] . " = {};\n";
						$htmlstr .= "\t var " . $v["field"] . "input = \$('." . $v["field"] . "-line');\n";
						$htmlstr .= "\t for (var i = 0; i < " . $v["field"] . "input.length; i++) {\n";
						$htmlstr .= "\t\tif(" . $v["field"] . "input.eq(i).find('input').eq(0).val() !== ''){\n";
						$htmlstr .= "\t \t\t" . $v["field"] . "[" . $v["field"] . "input.eq(i).find('input').eq(0).val()] = " . $v["field"] . "input.eq(i).find('input').eq(1).val();\n";
						$htmlstr .= "\t\t}\n";
						$htmlstr .= "\t };\n";
					}
					if ($v["type"] == 34) {
						$htmlstr .= "\t var " . $v["field"] . " = [];\n";
						$htmlstr .= "\t \$(\"." . $v["field"] . " i\").each(function() {\n";
						$htmlstr .= "\t\t" . $v["field"] . ".push(\$(this).text());\n";
						$htmlstr .= "\t });\n";
					}
				}
			}
			$htmlstr .= "\t var ajax = new \$ax(Feng.ctxPath + \"/" . getUrlName($menuInfo["controller_name"]) . "/" . $actionInfo["action_name"] . "\", function (data) {\n";
			$htmlstr .= "\t \tif ('00' === data.status) {\n";
			$htmlstr .= "\t \t\tFeng.success(data.msg,1000);\n";
			if ($menuInfo["menu_id"] != config("my.config_module_id")) {
				$htmlstr .= "\t \t\twindow.parent.CodeGoods.table.refresh();\n";
			}
			$htmlstr .= "\t \t\tCodeInfoDlg.close();\n";
			$htmlstr .= "\t \t} else {\n";
			$htmlstr .= "\t \t\tFeng.error(data.msg + \"！\",1000);\n";
			$htmlstr .= "\t\t }\n";
			$htmlstr .= "\t })\n";
			if ($fieldList) {
				foreach ($fieldList as $k => $v) {
					if (in_array($v["field"], explode(",", $actionInfo["fields"] . $relateFields))) {
						if (in_array($v["type"], [3, 4, 16, 23])) {
							$htmlstr .= "\t ajax.set('" . $v["field"] . "'," . $v["field"] . ");\n";
						}
						if (in_array($v["type"], [9, 32])) {
							$htmlstr .= "\t ajax.set('" . $v["field"] . "',(JSON.stringify(" . $v["field"] . ") == '{}' || JSON.stringify(" . $v["field"] . ") == '{\"\":\"\"}') ? '' : JSON.stringify(" . $v["field"] . "));\n";
						}
						if ($v["type"] == 33) {
							$htmlstr .= "\t ajax.set('" . $v["field"] . "'," . $v["field"] . ".getMarkdown());\n";
						}
						if ($v["type"] == 34) {
							$htmlstr .= "\t ajax.set('" . $v["field"] . "'," . $v["field"] . ".join(\"|\"));\n";
						}
					}
				}
			}
			if ($val["relate_table"] && $val["relate_field"]) {
				$relateTableFieldList = service\BuildService::getRelateFieldList($val["relate_table"]);
				if ($relateTableFieldList) {
					foreach ($relateTableFieldList as $k => $v) {
						if ($v["type"] == 3 || $v["type"] == 23) {
							$htmlstr .= "\t var " . $v["field"] . " = \$(\"input[name = '" . $v["field"] . "']:checked\").val();\n";
						} elseif ($v["type"] == 4) {
							$htmlstr .= "\t var " . $v["field"] . " = '';\n";
							$htmlstr .= "\t \$('input[name=\"" . $v["field"] . "\"]:checked').each(function(){ \n";
							$htmlstr .= "\t \t" . $v["field"] . " += ',' + \$(this).val(); \n";
							$htmlstr .= "\t }); \n";
							$htmlstr .= "\t  " . $v["field"] . " = " . $v["field"] . ".substr(1); \n";
						} elseif ($v["type"] == 9) {
							$htmlstr .= "\t var " . $v["field"] . " = '';\n";
							$htmlstr .= "\t \$(\"." . $v["field"] . " img\").each(function() {\n";
							$htmlstr .= "\t \t" . $v["field"] . " += '|'+\$(this).attr('src');\n";
							$htmlstr .= "\t });\n";
							$htmlstr .= "\t " . $v["field"] . " = " . $v["field"] . ".substr(1);\n";
						} elseif ($v["type"] == 16) {
							$htmlstr .= "\t var " . $v["field"] . " = UE.getEditor('" . $v["field"] . "').getContent();\n";
						} else {
							$htmlstr .= "\t var " . $v["field"] . " = \$('#" . $v["field"] . "').val();\n";
						}
					}
					foreach ($relateTableFieldList as $k => $v) {
						$htmlstr .= "\t ajax.set('" . $v["field"] . "'," . $v["field"] . ");\n";
					}
				}
			}
			$htmlstr .= "\t ajax.set(this.CodeInfoData);\n";
			$htmlstr .= "\t ajax.start();\n";
			$htmlstr .= "};\n\n\n";
		}
		if ($actionInfo["type"] == 7 || $actionInfo["type"] == 8) {
			$htmlstr .= "CodeInfoDlg." . $actionInfo["action_name"] . " = function () {\n";
			$htmlstr .= "\t this.clearData();\n";
			$htmlstr .= "\t this.collectData();\n";
			$htmlstr .= "\t if (!this.validate()) {\n";
			$htmlstr .= "\t \treturn;\n";
			$htmlstr .= "\t }\n";
			$htmlstr .= "\t var tip = '操作';\n";
			$htmlstr .= "\t var ajax = new \$ax(Feng.ctxPath + \"/" . getUrlName($menuInfo["controller_name"]) . "/" . $actionInfo["action_name"] . "\", function (data) {\n";
			$htmlstr .= "\t \tif ('00' === data.status) {\n";
			$htmlstr .= "\t \t\tFeng.success(tip + \"成功\" );\n";
			$htmlstr .= "\t \t\twindow.parent.CodeGoods.table.refresh();\n";
			$htmlstr .= "\t \t\tCodeInfoDlg.close();\n";
			$htmlstr .= "\t \t} else {\n";
			$htmlstr .= "\t \t\tFeng.error(data.msg + \"！\",1000);\n";
			$htmlstr .= "\t\t }\n";
			$htmlstr .= "\t }, function (data) {\n";
			$htmlstr .= "\t \tFeng.error(\"操作失败!\" + data.responseJSON.message + \"!\");\n";
			$htmlstr .= "\t });\n";
			$htmlstr .= "\t ajax.set(this.CodeInfoData);\n";
			$htmlstr .= "\t ajax.start();\n";
			$htmlstr .= "};\n\n\n";
		}
		if ($actionInfo["type"] == 9) {
			$htmlstr .= "CodeInfoDlg." . $actionInfo["action_name"] . " = function () {\n";
			$htmlstr .= "\t this.clearData();\n";
			$htmlstr .= "\t this.collectData();\n";
			$htmlstr .= "\t if (!this.validate()) {\n";
			$htmlstr .= "\t \treturn;\n";
			$htmlstr .= "\t }\n";
			$htmlstr .= "\t var tip = '操作';\n";
			$htmlstr .= "\t var ajax = new \$ax(Feng.ctxPath + \"/" . getUrlName($menuInfo["controller_name"]) . "/" . $actionInfo["action_name"] . "\", function (data) {\n";
			$htmlstr .= "\t \tif ('00' === data.status) {\n";
			$htmlstr .= "\t \t\tFeng.success(tip + \"成功\" );\n";
			$htmlstr .= "\t \t\twindow.parent.CodeGoods.table.refresh();\n";
			$htmlstr .= "\t \t\tCodeInfoDlg.close();\n";
			$htmlstr .= "\t \t} else {\n";
			$htmlstr .= "\t \t\tFeng.error(data.msg + \"！\",1000);\n";
			$htmlstr .= "\t\t }\n";
			$htmlstr .= "\t }, function (data) {\n";
			$htmlstr .= "\t \tFeng.error(\"操作失败!\" + data.responseJSON.message + \"!\");\n";
			$htmlstr .= "\t });\n";
			$htmlstr .= "\t ajax.set(this.CodeInfoData);\n";
			$htmlstr .= "\t ajax.start();\n";
			$htmlstr .= "};\n\n\n";
		}
		$htmlstr .= "</script>\n";
		$htmlstr .= "<script src=\"__PUBLIC__/static/js/base.js\" charset=\"utf-8\"></script>\n";
		$htmlstr .= "{/block}\n";
		$rootPath = app()->getRootPath();
		$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/view/" . getViewName($menuInfo["controller_name"]) . "/" . $actionInfo["action_name"] . ".html";
		filePutContents($htmlstr, $filepath, $type = 2);
	}
	public static function normalForm($fieldList, $actionInfo, $menuInfo, $applicationInfo)
	{
		$htmlstr = "";
		$htmlstr .= "{extend name='common/_container'}\n";
		$htmlstr .= "{block name=\"content\"}\n";
		$htmlstr .= "<div class=\"ibox float-e-margins\">\n";
		if ($actionInfo["type"] != 3) {
			$htmlstr .= "<input type=\"hidden\" name='" . $menuInfo["pk_id"] . "' id='" . $menuInfo["pk_id"] . "' value=\"{\$info." . $menuInfo["pk_id"] . "}\" />\n";
		}
		$htmlstr .= "\t<div class=\"ibox-content\">\n";
		$htmlstr .= "\t\t<div class=\"form-horizontal\" id=\"CodeInfoForm\">\n";
		$htmlstr .= "\t\t\t<div class=\"row\">\n";
		$htmlstr .= "\t\t\t\t<div class=\"col-sm-12\">\n";
		$htmlstr .= "\t\t\t\t<!-- form start -->\n";
		if ($fieldList) {
			foreach ($fieldList as $key => $val) {
				if ($val["is_post"] == 1 && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
					$htmlstr .= service\BuildService::formGroup($val, $actionInfo["type"], $applicationInfo, $menuInfo);
				}
			}
		}
		$htmlstr .= "\t\t\t\t<!-- form end -->\n";
		$htmlstr .= "\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t</div>\n";
		return $htmlstr;
	}
	public static function tabForm($fieldlist, $actionInfo, $tabList, $menuInfo, $applicationInfo)
	{
		$htmlstr = "";
		$htmlstr .= "{extend name='common/_container'}\n";
		$htmlstr .= "{block name=\"content\"}\n";
		$htmlstr .= "<div class=\"ibox float-e-margins\">\n";
		if ($actionInfo["type"] != 3) {
			$htmlstr .= "<input type=\"hidden\" name='" . $menuInfo["pk_id"] . "' id='" . $menuInfo["pk_id"] . "' value=\"{\$info." . $menuInfo["pk_id"] . "}\" />\n";
		}
		$htmlstr .= "\t<div class=\"ibox-content\">\n";
		$htmlstr .= "\t\t<div class=\"form-horizontal\" id=\"CodeInfoForm\">\n";
		$htmlstr .= "\t\t\t<div class=\"row\" style=\"margin-top:-20px;\">\n";
		$htmlstr .= "\t\t\t\t<div class=\"layui-tab layui-tab-brief\" lay-filter=\"test\">\n";
		if ($tabList) {
			$htmlstr .= "\t\t\t\t\t<ul class=\"layui-tab-title\">\n";
			foreach ($tabList as $key => $val) {
				if ($key == 0) {
					$htmlstr .= "\t\t\t\t\t\t<li class=\"layui-this\">" . $val . "</li>\n";
				} else {
					$htmlstr .= "\t\t\t\t\t\t<li>" . $val . "</li>\n";
				}
			}
			$htmlstr .= "\t\t\t\t\t</ul>\n";
		}
		$htmlstr .= "\t\t\t\t\t<div class=\"layui-tab-content\" style=\"margin-top:10px;\">\n";
		if ($tabList) {
			foreach ($tabList as $k => $v) {
				if ($k == 0) {
					$htmlstr .= "\t\t\t\t\t\t<div class=\"layui-tab-item layui-show\">\n";
				} else {
					$htmlstr .= "\t\t\t\t\t\t<div class=\"layui-tab-item\">\n";
				}
				$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-12\">\n";
				$htmlstr .= "\t\t\t\t\t\t\t<!-- form start -->\n";
				if ($fieldlist) {
					foreach ($fieldlist as $key => $val) {
						if ($val["is_post"] == 1 && $val["tab_menu_name"] == $v && in_array($val["field"], explode(",", $actionInfo["fields"]))) {
							$htmlstr .= service\BuildService::formGroup($val, $actionInfo["type"], $applicationInfo, $menuInfo);
						}
					}
				}
				$htmlstr .= "\t\t\t\t\t\t\t<!-- form end -->\n";
				$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
				$htmlstr .= "\t\t\t\t\t\t</div>\n";
			}
		}
		$htmlstr .= "\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t</div>\n";
		return $htmlstr;
	}
	public static function createViewTpl($applicationInfo, $menuInfo, $actionInfo, $fieldList)
	{
		$htmlstr .= "{extend name='common/_container'} \n";
		$htmlstr .= "{block name=\"content\"} \n";
		$htmlstr .= "<div class=\"ibox float-e-margins\"> \n";
		$htmlstr .= "\t<div class=\"ibox-content\"> \n";
		$htmlstr .= "\t\t<table class=\"table table-bordered\" style=\"word-break:break-all;\"> \n";
		$htmlstr .= "\t\t\t<tbody> \n";
		foreach ($fieldList as $key => $val) {
			if (in_array($val["field"], explode(",", $actionInfo["fields"]))) {
				$htmlstr .= "\t\t\t\t<tr> \n";
				$htmlstr .= "\t\t\t\t\t<td style=\"background-color:#F5F5F6; font-weight:bold; text-align:right\" width=\"15%\">" . $val["name"] . "：</td> \n";
				$default_time_format = explode("|", $val["default_value"]);
				$time_format = $default_time_format[0];
				if (!$time_format || $val["default_value"] == "null") {
					$time_format = "Y-m-d H:i:s";
				}
				switch ($val["type"]) {
					case 2:
						if (empty($val["sql"])) {
							$fieldval = "<?php echo getFieldVal(\$info[\"" . $val["field"] . "\"],\"" . $val["config"] . "\");?>";
						} else {
							$fieldval = "{\$info." . $val["field"] . "}";
						}
						break;
					case 3:
						if (empty($val["sql"])) {
							$fieldval = "<?php echo getFieldVal(\$info[\"" . $val["field"] . "\"],\"" . $val["config"] . "\");?>";
						} else {
							$fieldval = "{\$info." . $val["field"] . "}";
						}
						break;
					case 4:
						if (empty($val["sql"])) {
							$fieldval = "<?php echo getFieldVal(\$info[\"" . $val["field"] . "\"],\"" . $val["config"] . "\");?>";
						} else {
							$fieldval = "{\$info." . $val["field"] . "}";
						}
						break;
					case 23:
						$fieldval = "<?php echo getFieldVal(\$info[\"" . $val["field"] . "\"],\"" . $val["config"] . "\");?>";
						break;
					case 7:
						$fieldval = "{if \$info." . $val["field"] . "}{\$info." . $val["field"] . "|date='" . $time_format . "'}{/if}";
						break;
					case 8:
						$fieldval = "<a href=\"javascript:void(0)\"  onclick=\"openImg('{\$info." . $val["field"] . "}')\"><img height=\"75\" src=\"{\$info." . $val["field"] . "}\"></a>";
						break;
					case 9:
						$fieldval = "\n";
						$fieldval .= "\t\t\t\t\t\t<ul>\n";
						$fieldval .= "\t\t\t\t\t\t<?php \$" . $val["field"] . "List = json_decode(html_out(\$info[\"" . $val["field"] . "\"]),true);?>\n";
						$fieldval .= "\t\t\t\t\t\t{foreach name=\"" . $val["field"] . "List\" id=\"vo\"}\n";
						$fieldval .= "\t\t\t\t\t\t<li style=\"float:left; margin-bottom:2px; margin-right:2px;\"><a href=\"javascript:void(0)\" onclick=\"openImg('{\$vo.url}')\"><img src=\"{\$vo.url}\" height=\"75\"></a></li>\n";
						$fieldval .= "\t\t\t\t\t\t{/foreach}\n";
						$fieldval .= "\t\t\t\t\t\t</ul>\n";
						break;
					case 10:
						$fieldval = "<a target=\"_blank\" href=\"{\$info." . $val["field"] . "}\">下载附件</a>";
						break;
					case 11:
						$fieldval = "{\$info." . $val["field"] . "|html_out}";
						break;
					case 12:
						$fieldval = "{if \$info." . $val["field"] . "}{\$info." . $val["field"] . "|date='" . $time_format . "'}{/if}";
						break;
					case 16:
						$fieldval = "{\$info." . $val["field"] . "|html_out}";
						break;
					case 17:
						$areaval = "";
						foreach (explode("|", $val["field"]) as $m => $n) {
							$areaval .= "{\$info." . $n . "}" . "-";
						}
						$fieldval = rtrim($areaval, "-");
						break;
					case 25:
						$fieldval = "{if \$info." . $val["field"] . "}{\$info." . $val["field"] . "|date='" . $time_format . "'}{/if}";
						break;
					case 27:
						if (empty($val["sql"])) {
							$fieldval = "<?php echo getFieldVal(\$info[\"" . $val["field"] . "\"],\"" . $val["config"] . "\");?>";
						} else {
							$fieldval = "{\$info." . $val["field"] . "}";
						}
						break;
					case 29:
						if (empty($val["sql"])) {
							$fieldval = "<?php echo getFieldVal(\$info[\"" . $val["field"] . "\"],\"" . $val["config"] . "\");?>";
						} else {
							$fieldval = "{\$info." . $val["field"] . "}";
						}
						break;
					case 34:
						$fieldval = "\n";
						$fieldval .= "\t\t\t\t\t\t<?php \$" . $val["field"] . "List = explode('|',\$info['" . $val["field"] . "']);?>\n";
						$fieldval .= "\t\t\t\t\t\t{foreach name=\"" . $val["field"] . "List\" id=\"vo\"}\n";
						$fieldval .= "\t\t\t\t\t\t<a href=\"{\$vo}\" >附件下载{\$key+1}</a>&nbsp;&nbsp;\n";
						$fieldval .= "\t\t\t\t\t\t{/foreach}\n";
						break;
					default:
						$fieldval = "{\$info." . $val["field"] . "}";
				}
				$htmlstr .= "\t\t\t\t\t<td>" . $fieldval . "</td>   \n";
				$htmlstr .= "\t\t\t\t</tr> \n";
			}
		}
		$htmlstr .= "\t\t\t</tbody> \n";
		$htmlstr .= "\t\t</table> \n";
		$htmlstr .= "\t</div> \n";
		$htmlstr .= "</div> \n";
		$htmlstr .= "{/block} \n";
		$rootPath = app()->getRootPath();
		$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/view/" . getViewName($menuInfo["controller_name"]) . "/" . $actionInfo["action_name"] . ".html";
		filePutContents($htmlstr, $filepath, $type = 2);
	}
	public static function createConfig($applicationInfo, $menuInfo)
	{
		$tabList = service\FieldSetService::tabList($menuInfo["menu_id"]);
		$fieldList = model\Field::where(["menu_id" => config("my.config_module_id"), "is_post" => 1])->order("sortid asc")->select();
		$htmlstr = "";
		$htmlstr .= "{extend name='common/_container'}\n";
		$htmlstr .= "{block name=\"content\"}\n";
		$htmlstr .= "<div class=\"ibox float-e-margins\">\n";
		$htmlstr .= "\t<div class=\"ibox-content\">\n";
		$htmlstr .= "\t\t<div class=\"form-horizontal\" id=\"CodeInfoForm\">\n";
		$htmlstr .= "\t\t\t<div class=\"row\">\n";
		$htmlstr .= "\t\t\t\t<div class=\"layui-tab layui-tab-brief\" lay-filter=\"test\">\n";
		if ($tabList) {
			$htmlstr .= "\t\t\t\t\t<ul class=\"layui-tab-title\">\n";
			foreach ($tabList as $key => $val) {
				if ($key == 0) {
					$htmlstr .= "\t\t\t\t\t\t<li class=\"layui-this\">" . $val . "</li>\n";
				} else {
					$htmlstr .= "\t\t\t\t\t\t<li>" . $val . "</li>\n";
				}
			}
			$htmlstr .= "\t\t\t\t\t</ul>\n";
		} else {
			$htmlstr .= "\t\t\t\t\t<ul class=\"layui-tab-title\">\n";
			$htmlstr .= "\t\t\t\t\t\t<li class=\"layui-this\">系统配置</li>\n";
			$htmlstr .= "\t\t\t\t\t</ul>\n";
		}
		$htmlstr .= "\t\t\t\t\t<div class=\"layui-tab-content\" style=\"margin-top:10px;\">\n";
		if ($tabList) {
			foreach ($tabList as $k => $v) {
				if ($k == 0) {
					$htmlstr .= "\t\t\t\t\t\t<div class=\"layui-tab-item layui-show\">\n";
				} else {
					$htmlstr .= "\t\t\t\t\t\t<div class=\"layui-tab-item\">\n";
				}
				$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n";
				$htmlstr .= "\t\t\t\t\t\t\t<!-- form start -->\n";
				if ($fieldList) {
					foreach ($fieldList as $key => $val) {
						if ($val["is_post"] == 1 && $val["tab_menu_name"] == $v) {
							$htmlstr .= service\BuildService::formGroup($val, 4, $applicationInfo, $menuInfo);
						}
					}
				}
				$htmlstr .= "\t\t\t\t\t\t\t<!-- form end -->\n";
				$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
				$htmlstr .= "\t\t\t\t\t\t</div>\n";
			}
		} else {
			$htmlstr .= "\t\t\t\t\t\t<div class=\"layui-tab-item layui-show\">\n";
			$htmlstr .= "\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n";
			$htmlstr .= "\t\t\t\t\t\t\t<!-- form start -->\n";
			if ($fieldList) {
				foreach ($fieldList as $key => $val) {
					if ($val["is_post"] == 1) {
						$htmlstr .= service\BuildService::formGroup($val, 4, $applicationInfo, $menuInfo);
					}
				}
			}
			$htmlstr .= "\t\t\t\t\t\t\t<!-- form end -->\n";
			$htmlstr .= "\t\t\t\t\t\t\t</div>\n";
			$htmlstr .= "\t\t\t\t\t\t</div>\n";
		}
		$htmlstr .= "\t\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t</div>\n";
		$htmlstr .= "\t\t\t<div class=\"hr-line-dashed\"></div>\n";
		$htmlstr .= "\t\t\t<div class=\"row btn-group-m-t\">\n";
		$htmlstr .= "\t\t\t\t<div class=\"col-sm-10\">\n";
		$htmlstr .= "\t\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" onclick=\"CodeInfoDlg.index()\" id=\"ensure\">\n";
		$htmlstr .= "\t\t\t\t\t\t<i class=\"fa fa-check\"></i>&nbsp;确认提交\n";
		$htmlstr .= "\t\t\t\t\t</button>\n";
		$htmlstr .= "\t\t\t\t\t<button type=\"button\" class=\"btn btn-danger\" onclick=\"CodeInfoDlg.close()\" id=\"cancel\">\n";
		$htmlstr .= "\t\t\t\t\t\t<i class=\"fa fa-eraser\"></i>&nbsp;取消\n";
		$htmlstr .= "\t\t\t\t\t</button>\n";
		$htmlstr .= "\t\t\t\t</div>\n";
		$htmlstr .= "\t\t\t</div>\n";
		$htmlstr .= "\t\t</div>\n";
		$htmlstr .= "\t</div>\n";
		$htmlstr .= "</div>\n";
		$htmlstr .= "<script src=\"__PUBLIC__/static/js/upload.js\" charset=\"utf-8\"></script>\n";
		$htmlstr .= "<script src=\"__PUBLIC__/static/js/plugins/layui/layui.js?t=1498856285724\" charset=\"utf-8\"></script>\n";
		foreach ($fieldList as $key => $val) {
			if (in_array($val["type"], [27, 29])) {
				$chosen_status = true;
			}
			if ($val["type"] == 28) {
				$tag_status = true;
			}
			if ($val["type"] == 9) {
				$images_status = true;
			}
		}
		if ($chosen_status) {
			$htmlstr .= "<link href='__PUBLIC__/static/js/plugins/chosen/chosen.min.css' rel='stylesheet'/>\n";
			$htmlstr .= "<script src='__PUBLIC__/static/js/plugins/chosen/chosen.jquery.js'></script>\n";
		}
		if ($tag_status) {
			$htmlstr .= "<link rel='stylesheet' href='__PUBLIC__/static/js/plugins/tagsinput/tagsinput.css'>\n";
			$htmlstr .= "<script type='text/javascript' src='__PUBLIC__/static/js/plugins/tagsinput/tagsinput.min.js'></script>\n";
		}
		if ($images_status) {
			$htmlstr .= "<script src='__PUBLIC__/static/js/plugins/paixu/jquery-migrate-1.1.1.js'></script>\n";
			$htmlstr .= "<script src='__PUBLIC__/static/js/plugins/paixu/jquery.dragsort-0.5.1.min.js'></script>\n";
		}
		$htmlstr .= "<script>\n";
		if ($images_status) {
			$htmlstr .= "\$(function(){\n";
			$htmlstr .= "\t\$(\".filelist\").dragsort({dragSelector: \"img\",dragBetween: true ,dragEnd:function(){}});\n";
			foreach ($fieldList as $key => $val) {
				if ($val["type"] == 32) {
					$htmlstr .= "\t\$(\"." . $val["field"] . "\").dragsort({dragSelector: \".move\",dragBetween: true ,dragEnd:function(){}});\n";
				}
			}
			$htmlstr .= "});\n";
		}
		$htmlstr .= "layui.use(['form'],function(){});\n";
		$htmlstr .= "layui.use('element', function(){\n";
		$htmlstr .= "\tvar element = layui.element;\n";
		$htmlstr .= "\telement.on('tab(test)', function(elem){\n";
		$firstMenuName = $tabList[0];
		unset($tabList[0]);
		foreach ($fieldList as $key => $val) {
			if ($val["type"] == 8 && $val["is_post"] == 1 && in_array($val["tab_menu_name"], $tabList)) {
				$htmlstr .= "\t\tuploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',false,'','{:url(\"" . $applicationInfo["app_dir"] . "/Upload/uploadImages" . "\")}');\n";
			}
			if ($val["type"] == 10 && $val["is_post"] == 1 && in_array($val["tab_menu_name"], $tabList)) {
				$htmlstr .= "\t\tuploader('" . $val["field"] . "_upload','" . $val["field"] . "','file',false,'','{:url(\"" . $applicationInfo["app_dir"] . "/Upload/uploadImages" . "\")}');\n";
			}
		}
		$htmlstr .= "\t});\n";
		$htmlstr .= "});\n";
		if ($fieldList) {
			foreach ($fieldList as $key => $val) {
				if ($val["type"] == 8 && $val["is_post"] == 1) {
					if ($menuInfo["upload_config_id"]) {
						$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',false,'','{:getUploadServerUrl(" . $menuInfo["upload_config_id"] . "])}');\n";
					} else {
						$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',false,'','{:getUploadServerUrl()}');\n";
					}
				}
				if ($val["type"] == 9 && $val["is_post"] == 1) {
					if ($menuInfo["upload_config_id"]) {
						$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',true,'{\$info." . $val["field"] . "}','{:getUploadServerUrl(" . $menuInfo["upload_config_id"] . "])}');\n";
					} else {
						$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','image',true,'{\$info." . $val["field"] . "}','{:getUploadServerUrl()}');\n";
					}
					$htmlstr .= "setUploadButton('" . $val["field"] . "_upload');\n";
				}
				if ($val["type"] == 10 && $val["is_post"] == 1) {
					$htmlstr .= "uploader('" . $val["field"] . "_upload','" . $val["field"] . "','file',false,'','{:getUploadServerUrl()}');\n";
				}
			}
		}
		foreach ($fieldList as $key => $val) {
			if ($val["type"] == 27) {
				$htmlstr .= "\$(function(){\$('.chosen').chosen({})})\n";
			}
		}
		foreach ($fieldList as $key => $val) {
			if (in_array($val["type"], [7, 12, 25])) {
				$dateList = service\FieldSetService::dateList();
				$default_value = explode("|", $val["default_value"]);
				$time_format = $dateList[$default_value[0]];
				if (!$time_format || $val["default_value"] == "null") {
					$time_format = "datetime";
				}
				$htmlstr .= "laydate.render({elem: '#" . $val["field"] . "',type: '" . $time_format . "',trigger:'click'});\n";
			}
		}
		$htmlstr .= "var CodeInfoDlg = {\n";
		$htmlstr .= "\tCodeInfoData: {},\n";
		$htmlstr .= "\tvalidateFields: {\n";
		foreach ($fieldList as $key => $val) {
			$val = checkData($val);
			if ((!empty($val["validate"]) || !empty($val["rule"])) && $val["type"] != 17) {
				$htmlstr .= "\t\t" . $val["field"] . ": {\n";
				$htmlstr .= "\t\t\tvalidators: {\n";
				if (in_array("notEmpty", explode(",", $val["validate"]))) {
					$htmlstr .= "\t\t\t\tnotEmpty: {\n";
					$htmlstr .= "\t\t\t\t\tmessage: '" . $val["name"] . "不能为空'\n";
					$htmlstr .= "\t \t\t\t},\n";
				}
				if (!empty($val["rule"])) {
					$htmlstr .= "\t\t\t\tregexp: {\n";
					$htmlstr .= "\t\t\t\t\tregexp: " . $val["rule"] . ",\n";
					$htmlstr .= "\t\t\t\t\tmessage: '" . $val["message"] . "'\n";
					$htmlstr .= "\t \t\t\t},\n";
				}
				$htmlstr .= "\t \t\t}\n";
				$htmlstr .= "\t \t},\n";
			}
		}
		$htmlstr .= "\t }\n";
		$htmlstr .= "}\n\n";
		$htmlstr .= "CodeInfoDlg.collectData = function () {\n";
		$htmlstr .= "\tthis";
		foreach ($fieldList as $key => $val) {
			if (!in_array($val["type"], [3, 4, 9, 16, 17, 23, 32, 33])) {
				$htmlstr .= ".set('" . $val["field"] . "')";
			}
			if ($val["type"] == 17 && !empty($val["field"])) {
				foreach (explode("|", $val["field"]) as $k => $v) {
					$htmlstr .= ".set('" . $v . "')";
				}
			}
		}
		$htmlstr .= ";\n";
		$htmlstr .= "};\n\n";
		$htmlstr .= "CodeInfoDlg.index = function () {\n";
		$htmlstr .= "\t this.clearData();\n";
		$htmlstr .= "\t this.collectData();\n";
		$htmlstr .= "\t if (!this.validate()) {\n";
		$htmlstr .= "\t \treturn;\n";
		$htmlstr .= "\t }\n";
		foreach ($fieldList as $k => $v) {
			if ($v["type"] == 3 || $v["type"] == 23) {
				$htmlstr .= "\t var " . $v["field"] . " = \$(\"input[name = '" . $v["field"] . "']:checked\").val();\n";
			}
			if ($v["type"] == 4) {
				$htmlstr .= "\t var " . $v["field"] . " = '';\n";
				$htmlstr .= "\t \$('input[name=\"" . $v["field"] . "\"]:checked').each(function(){ \n";
				$htmlstr .= "\t \t" . $v["field"] . " += ',' + \$(this).val(); \n";
				$htmlstr .= "\t }); \n";
				$htmlstr .= "\t  " . $v["field"] . " = " . $v["field"] . ".substr(1); \n";
			}
			if ($v["type"] == 9) {
				$htmlstr .= "\t var " . $v["field"] . " = {};\n";
				$htmlstr .= "\t \$(\"." . $v["field"] . " li\").each(function() {\n";
				$htmlstr .= "\t\tif(\$(this).find('img').attr('src')){\n";
				$htmlstr .= "\t \t\t" . $v["field"] . "[\$(this).index()] = {'url':\$(this).find('img').attr('src'),'title':\$(this).find('input').val()};\n";
				$htmlstr .= "\t\t}\n";
				$htmlstr .= "\t });\n";
			}
			if ($v["type"] == 16) {
				$htmlstr .= "\t var " . $v["field"] . " = UE.getEditor('" . $v["field"] . "').getContent();\n";
			}
			if ($v["type"] == 32) {
				$htmlstr .= "\t var " . $v["field"] . " = {};\n";
				$htmlstr .= "\t var " . $v["field"] . "input = \$('." . $v["field"] . "-line');\n";
				$htmlstr .= "\t for (var i = 0; i < " . $v["field"] . "input.length; i++) {\n";
				$htmlstr .= "\t\tif(" . $v["field"] . "input.eq(i).find('input').eq(0).val() !== ''){\n";
				$htmlstr .= "\t \t\t" . $v["field"] . "[" . $v["field"] . "input.eq(i).find('input').eq(0).val()] = " . $v["field"] . "input.eq(i).find('input').eq(1).val();\n";
				$htmlstr .= "\t\t}\n";
				$htmlstr .= "\t };\n";
			}
		}
		$htmlstr .= "\t var ajax = new \$ax(Feng.ctxPath + \"/Base/config\", function (data) {\n";
		$htmlstr .= "\t \tif ('00' === data.status) {\n";
		$htmlstr .= "\t \t\tFeng.success(data.msg,1000);\n";
		$htmlstr .= "\t \t} else {\n";
		$htmlstr .= "\t \t\tFeng.error(data.msg + \"！\",1000);\n";
		$htmlstr .= "\t\t }\n";
		$htmlstr .= "\t })\n";
		if ($fieldList) {
			foreach ($fieldList as $k => $v) {
				if (in_array($v["type"], [3, 4, 16, 23])) {
					$htmlstr .= "\t ajax.set('" . $v["field"] . "'," . $v["field"] . ");\n";
				}
				if (in_array($v["type"], [9, 32])) {
					$htmlstr .= "\t ajax.set('" . $v["field"] . "',(JSON.stringify(" . $v["field"] . ") == '{}' || JSON.stringify(" . $v["field"] . ") == '{\"\":\"\"}') ? '' : JSON.stringify(" . $v["field"] . "));\n";
				}
				if ($v["type"] == 33) {
					$htmlstr .= "\t ajax.set('" . $v["field"] . "'," . $v["field"] . ".getMarkdown());\n";
				}
			}
		}
		$htmlstr .= "\t ajax.set(this.CodeInfoData);\n";
		$htmlstr .= "\t ajax.start();\n";
		$htmlstr .= "};\n";
		$htmlstr .= "</script>\n\n\n";
		$htmlstr .= "<script src=\"__PUBLIC__/static/js/base.js\" charset=\"utf-8\"></script>\n";
		$htmlstr .= "{/block}\n";
		$htmlstr = str_replace("col-sm-10", "col-sm-7", $htmlstr);
		$htmlstr = str_replace("col-sm-3", "col-sm-2", $htmlstr);
		$htmlstr = str_replace("col-sm-6", "col-sm-5", $htmlstr);
		$rootPath = app()->getRootPath();
		$filepath = $rootPath . "/app/gcadmin/view/base/config.html";
		filePutContents($htmlstr, $filepath, $type = 2);
	}
	public function createValidate($actionList, $applicationInfo, $menuInfo)
	{
		$str = "";
		$str = "<?php \n";
		!is_null(config("my.comment.file_comment")) ? config("my.comment.file_comment") : true;
		if (config("my.comment.file_comment")) {
			$str .= "/*\n";
			$str .= " module:\t\t" . $menuInfo["title"] . "验证器\n";
			$str .= " create_time:\t" . date("Y-m-d H:i:s") . "\n";
			$str .= " author:\t\t" . config("my.comment.author") . "\n";
			$str .= " contact:\t\t" . config("my.comment.contact") . "\n";
			$str .= "*/\n\n";
		}
		$str .= "namespace app\\" . $applicationInfo["app_dir"] . "\\validate" . getDbName($menuInfo["controller_name"]) . ";\n";
		$str .= "use think\\validate;\n";
		$fieldList = model\Field::where(["menu_id" => $menuInfo["menu_id"]])->order("sortid asc")->select()->toArray();
		$fieldList = htmlOutList($fieldList);
		$str .= "\n";
		$str .= "class " . getControllerName($menuInfo["controller_name"]) . " extends validate {\n\n\n";
		foreach ($fieldList as $k => $v) {
			if ((!empty($v["validate"]) || !empty($v["rule"])) && !in_array($v["type"], [12, 15, 21, 25, 26, 30])) {
				$rule .= "\t\t";
				if (in_array("notEmpty", explode(",", $v["validate"]))) {
					if ($v["type"] == 17) {
						foreach (explode("|", $v["field"]) as $m => $n) {
							if ($m < 2) {
								if ($m == 0) {
									$name = "所属省";
								}
								if ($m == 1) {
									$name = "所属市";
								}
								$msg .= "\t\t'" . $n . ".require'" . "=>" . "'" . $name . "不能为空',\n";
							}
						}
					} else {
						$rules .= "'require',";
						$msg .= "\t\t'" . $v["field"] . ".require'" . "=>" . "'" . $v["name"] . "不能为空',\n";
					}
				}
				if (in_array("unique", explode(",", $v["validate"]))) {
					$rules .= "'unique:" . $menuInfo["table_name"] . "',";
					$msg .= "\t\t'" . $v["field"] . ".unique'" . "=>" . "'" . $v["name"] . "已经存在',\n";
				}
				if (!empty($v["rule"])) {
					$rules .= "'regex'=>'" . $v["rule"] . "',";
					if (empty($v["message"])) {
						$msg .= "\t\t'" . $v["field"] . ".regex'" . "=>" . "'" . $v["name"] . "格式错误',\n";
					} else {
						$msg .= "\t\t'" . $v["field"] . ".regex'" . "=>" . "'" . $v["message"] . "',\n";
					}
				}
				if ($v["type"] == 17) {
					foreach (explode("|", $v["field"]) as $m => $n) {
						if ($m < 2) {
							$arearule .= "'" . $n . "'=>['require'],";
						}
					}
					$rule .= rtrim($arearule, ",");
				} else {
					$rule .= "'" . $v["field"] . "'=>[" . rtrim($rules, ",") . "]";
				}
				$rule .= ",\n";
				$rules = "";
				$validateFields[] = $v["field"];
			}
		}
		$scene = "";
		$fields = "";
		foreach ($actionList as $k => $v) {
			if (in_array($v["type"], [3, 4, 7, 8, 9]) && !empty($v["fields"]) && service\BuildService::checkValidateStatus($v["fields"], $validateFields)) {
				$fields = explode(",", $v["fields"]);
				foreach ($fields as $m => $j) {
					if (in_array($j, $validateFields)) {
						if (strpos($j, "|") > 0) {
							foreach (explode("|", $j) as $n) {
								$areafield .= ",'" . $n . "'";
							}
							$field .= $areafield;
						} else {
							$field .= ",'" . $j . "'";
						}
					}
				}
				$scene .= "\t\t'" . $v["action_name"] . "'=>[" . ltrim($field, ",") . "],\n";
			}
			$field = "";
			$areafield = "";
		}
		if ($rule) {
			$str .= "\tprotected \$rule = [\n" . $rule . "\t];\n\n";
			$str .= "\tprotected \$message = [\n" . rtrim($msg, ",") . "\t];\n\n";
			$str .= "\tprotected \$scene  = [\n" . $scene . "\t];\n\n";
			$rootPath = app()->getRootPath();
			$filepath = $rootPath . "/app/" . $applicationInfo["app_dir"] . "/validate/" . $menuInfo["controller_name"] . ".php";
			filePutContents($str, $filepath, $type = 1);
		}
	}
	private function createRoute($applicationInfo)
	{
		$str .= "<?php\n\n";
		$str .= "//接口路由文件\n\n";
		$str .= "use think\\facade\\Route;\n\n";
		$menuId = model\Menu::where(["app_id" => $applicationInfo["app_id"]])->column("menu_id");
		$where["menu_id"] = $menuId;
		$actionList = model\Action::where($where)->select();
		$middleware = "";
		if ($actionList) {
			foreach ($actionList as $key => $val) {
				if (!empty($val["api_auth"]) || !empty($val["sms_auth"]) || !empty($val["captcha_auth"])) {
					$menuInfo = model\Menu::find($val["menu_id"]);
					if (!empty($val["captcha_auth"])) {
						$middleware .= "'CaptchaAuth',";
					}
					if (!empty($val["api_auth"])) {
						$middleware .= "'JwtAuth',";
					}
					if (!empty($val["sms_auth"])) {
						$middleware .= "'SmsAuth'";
					}
					$str .= "Route::rule('" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . "', '" . getUrlName($menuInfo["controller_name"]) . "/" . $val["action_name"] . "')->middleware([" . rtrim($middleware, ",") . "]);\t//" . $menuInfo["title"] . $val["name"] . ";\n";
				}
				$middleware = "";
			}
		}
		$api_upload_auth = !is_null(config("my.api_upload_auth")) ? config("my.api_upload_auth") : true;
		if ($api_upload_auth) {
			$str .= "Route::rule('Base/Upload', 'Base/Upload')->middleware(['JwtAuth']);\t//图片上传;\n";
		}
		$rootPath = app()->getRootPath();
		$filepath = $rootPath . "app/" . $applicationInfo["app_dir"] . "/route/route.php";
		filePutContents($str, $filepath, $type = 3);
	}
}