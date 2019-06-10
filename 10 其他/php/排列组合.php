<?php

$arr = [1,2,3,4,5,6,7];
$need = 4;

// code
$tmp = [];
$allCount = 0;
getCompose($arr, $need);
var_dump($allCount);
echo PHP_EOL;

function getCompose($arr, $need){
	global $tmp;
	$tmpIndex = range(0, $need-1);
	$count = count($arr)-$need;
	//组合
	while (1) {
		$data = [];
		foreach ($tmpIndex as $item) {
			$data[] = $arr[$item];
		}
		$tmp = [];
		//echo implode('-', $data).PHP_EOL;
		getOrderList($data);

		$break = false;
		for ($i=$need-1; $i >= 0; $i--) {
			if ($tmpIndex[$i] >= $count+$i) {
				$break = true;
				continue;
			}
			$break = false;
			$index = $tmpIndex[$i];
			for ($j=$i; $j < $need; $j++) { 
				$index ++;
				$tmpIndex[$j] = $index;
			}
			break;
		}
		if($break) break;
	}
}

//排列
function getOrderList($arr = []) {
	global $tmp, $allCount;
	if (empty($arr)) {
		//echo implode('-', $tmp).PHP_EOL;
		$allCount++;
		return true;
	}
	
	for($i=count($arr)-1; $i>=0; $i--){
		$tmp[] = $arr[$i];
		$arr2 = $arr;
		unset($arr2[$i]);
		$arr2 = array_values($arr2);
		getOrderList($arr2);
		array_pop($tmp);
	}
	return false;
}
