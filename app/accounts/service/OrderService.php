<?php

//decode by http://www.yunlu99.com/
namespace app\accounts\service;

class OrderService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\accounts\model\Order::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function dumpData($list, $field, $schoolList)
	{
		ob_clean();
		try {
			$map["menu_id"] = 833;
			$map["field"] = $field;
			$fieldList = db("field")->where($map)->order("sortid asc")->select()->toArray();
			$fieldList[] = ["field" => "fx_store_money", "name" => "学校商家抽成"];
			$fieldList[] = ["field" => "s_name", "name" => "所属学校"];
			$fieldList[] = ["field" => "rider_name", "name" => "骑手姓名"];
			$fieldList[] = ["field" => "rider_phone", "name" => "骑手电话"];
			$fieldList[] = ["field" => "express_info", "name" => "取货单号"];
			$fieldList[] = ["field" => "business_name", "name" => "商家名称"];
			$fieldList[] = ["field" => "good_details", "name" => "商品信息"];
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			foreach ($fieldList as $key => $val) {
				if ($val["name"] == "跑腿被抽取的手续费") {
					$val["name"] = "平台跑腿抽成";
				}
				if ($val["name"] == "学校收取手续费") {
					$val["name"] = "学校跑腿抽成";
				}
				if ($val["name"] == "商家抽成费用") {
					$val["name"] = "平台商家抽成";
				}
				$sheet->setCellValue(getTag($key + 1) . "1", $val["name"]);
			}
			foreach ($list as $k => $v) {
				$rider = \think\facade\Db::name("wechat_user")->where("wxapp_id", $v["wxapp_id"])->where("openid", $v["end_openid"])->find();
				$v["rider_name"] = $rider["t_name"];
				$v["rider_phone"] = $rider["phone"];
				$v["s_name"] = $schoolList[$v["s_id"]];
				foreach ($fieldList as $m => $n) {
					if (in_array($n["type"], [7, 12, 25]) && $v[$n["field"]]) {
						$v[$n["field"]] = !empty($v[$n["field"]]) ? date(getTimeFormat($n), $v[$n["field"]]) : "";
					}
					if (in_array($n["type"], [2, 3, 4, 23, 27, 29]) && !empty($n["config"])) {
						$v[$n["field"]] = getFieldVal($v[$n["field"]], $n["config"]);
					}
					if ($n["type"] == 17) {
						foreach (explode("|", $n["field"]) as $q) {
							$v[$n["field"]] .= $v[$q] . "-";
						}
						$v[$n["field"]] = rtrim($v[$n["field"]], "-");
					}
					$sheet->setCellValueExplicit(getTag($m + 1) . ($k + 2), $v[$n["field"]], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
					$v[$n["field"]] = "";
				}
			}
			$filename = date("YmdHis");
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment;filename=" . $filename . "." . config("my.import_type"));
			header("Cache-Control: max-age=0");
			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save("php://output");
			exit;
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}
	public static function hszList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\accounts\model\Order::onlyTrashed()->where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
}