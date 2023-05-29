<?php

//decode by http://www.yunlu99.com/
function types($arr, $A, $B, $C, $D)
{
	$orderInfo = "";
	foreach ($arr as $k5 => $v5) {
		$name = $v5["name"];
		$price = $v5["price"];
		$num = "x" . $v5["num"];
		$prices = $v5["total"];
		$kw3 = "";
		$kw1 = "";
		$kw2 = "";
		$kw4 = "";
		$str = $name;
		$blankNum = $A;
		$lan = mb_strlen($str, "utf-8");
		$m = 0;
		$j = 1;
		$blankNum++;
		$result = [];
		if (strlen($price) < $B) {
			$k1 = $B - strlen($price);
			for ($q = 0; $q < $k1; $q++) {
				$kw1 .= " ";
			}
			$price = $price . $kw1;
		}
		if (strlen($num) < $C) {
			$k2 = $C - strlen($num);
			for ($q = 0; $q < $k2; $q++) {
				$kw2 .= " ";
			}
			$num = $num . $kw2;
		}
		if (strlen($prices) < $D) {
			$k3 = $D - strlen($prices);
			for ($q = 0; $q < $k3; $q++) {
				$kw4 .= " ";
			}
			$prices = $prices . $kw4;
		}
		for ($i = 0; $i < $lan; $i++) {
			$new = mb_substr($str, $m, $j, "utf-8");
			$j++;
			if (mb_strwidth($new, "utf-8") < $blankNum) {
				if ($lan < $m + $j) {
					$m = $m + $j;
					$tail = $new;
					$lenght = iconv("UTF-8", "GBK//IGNORE", $new);
					$k = $A - strlen($lenght);
					for ($q = 0; $q < $k; $q++) {
						$kw3 .= " ";
					}
					if ($m == $j) {
						$tail .= $kw3 . " " . $price . " " . $num . " " . $prices;
					} else {
						$tail .= $kw3 . "<BR>";
					}
					break;
				} else {
					$next_new = mb_substr($str, $m, $j, "utf-8");
					if (mb_strwidth($next_new, "utf-8") < $blankNum) {
					} else {
						$m = $i + 1;
						$result[] = $new;
						$j = 1;
					}
				}
			}
		}
		$head = "";
		foreach ($result as $key => $value) {
			if ($key < 1) {
				$v_lenght = iconv("UTF-8", "GBK//IGNORE", $value);
				$v_lenght = strlen($v_lenght);
				if ($v_lenght == 13) {
					$value = $value . " ";
				}
				$head .= $value . " " . $price . " " . $num . " " . $prices;
			} else {
				$head .= $value . "<BR>";
			}
		}
		$orderInfo .= $head . $tail;
		if (!empty($v5["specs"])) {
			$orderInfo .= $v5["specs"] . "<BR>";
		}
		if (!empty($v5["attri"])) {
			$v5["attri"] = ltrim($v5["attri"], "+");
			$orderInfo .= $v5["attri"] . "<BR>";
		}
	}
	$time = date("Y-m-d H:i:s", time());
	return $orderInfo;
}