<?php

//decode by http://www.yunlu99.com/
namespace app\gcadmin\service;

class LogService extends \xhadmin\CommonService
{
	public static function indexList($where, $field, $order, $limit, $page)
	{
		try {
			$res = \app\gcadmin\model\Log::where($where)->field($field)->order($order)->paginate(["list_rows" => $limit, "page" => $page])->toArray();
		} catch (\Exception $e) {
			abort(config("my.error_log_code"), $e->getMessage());
		}
		return ["rows" => $res["data"], "total" => $res["total"]];
	}
	public static function dumpData($list, $field)
	{
		ob_clean();
		try {
			$map["menu_id"] = 670;
			$map["field"] = $field;
			$fieldList = db("field")->where($map)->order("sortid asc")->select()->toArray();
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			foreach ($fieldList as $key => $val) {
				$sheet->setCellValue(getTag($key + 1) . "1", $val["name"]);
			}
			foreach ($list as $k => $v) {
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
}