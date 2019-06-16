<?php
/**
数独 9*9
 */
date_default_timezone_set('PRC');

$timer= new Timer();
$timer->start();


/*$data_origin = [
    [8,0,0,0,0,0,0,0,0],
    [0,0,3,6,0,0,0,0,0],
    [0,7,0,0,9,0,2,0,0],

    [0,5,0,0,0,7,0,0,0],
    [0,0,0,0,4,5,7,0,0],
    [0,0,0,1,0,0,0,3,0],

    [0,0,1,0,0,0,0,6,8],
    [0,0,8,5,0,0,0,1,0],
    [0,9,0,0,0,0,4,0,0],
];

$data_origin = [
    [0,2,0,0,0,0,0,1,0],
    [5,0,6,0,0,0,3,0,9],
    [0,8,0,5,0,2,0,6,0],

    [0,0,5,0,7,0,1,0,0],
    [0,0,0,2,0,8,0,0,0],
    [0,0,4,0,1,0,8,0,0],

    [0,5,0,8,0,7,0,3,0],
    [7,0,2,0,0,0,4,0,5],
    [0,4,0,0,0,0,0,7,0],
];*/

$data_origin = '
300201009
009000500
060000030
080502040
200000007
040306020
030000050
006000700
400907003
';

if(!is_array($data_origin)){
    $tmpStr = $data_origin;
    $data_origin = [];
    $i = 0;
    $index_new_array = -1;
    $index_count = 0;
    $strlen = strlen($tmpStr);
    while($i<$strlen){
        $tmp = $tmpStr{$i++};
        if('0a'==bin2hex($tmp)) continue;

        if($index_count%9==0){
            $index_new_array++;
        }
        $index_count++;
        $data_origin[$index_new_array][] = $tmp;
    }
}

echo '原始数据：' . PHP_EOL;
print_data($data_origin);
echo '运行中...' . PHP_EOL. PHP_EOL;

$i=0;
$link = [];
while(1)
{
    $i++;
    $isOver = FALSE;
    $data_col = [];
    $data_zone = [];
    $data_row = $data_origin;

    // 提取每个纵,每个区的值
    foreach ($data_row as $y => $data) {
        foreach ($data as $x => $item) {
            $data_col[$x][] = $item;
            $key = getZone($x, $y);
            $data_zone[$key][] = $item;
        }
    }

    // 获取每个键的可用数据
    $posibleData = [];
    foreach ($data_row as $y => $data) {
        foreach ($data as $x => $item) {
            if (empty($item)) {
                $tmp = getPosableData($x, $y);
                if(empty($tmp)){
                    $isOver = TRUE;
                    break 2;
                }
                $posibleData[$x . '_' . $y] = $tmp;
            }
        }
    }

    if($isOver){//退一步
        $reKeys  = [];
        getReKey($link);
        foreach ($reKeys as $key=>$val) {
            list($x, $y) = explode('_', $key);
            $data_origin[$y][$x] = $val;
        }
        // test
        // print_data($data_origin);
        continue;
    }

    if(empty($posibleData)){//成功了
        break;
    }

    // 获取最少可能的项
    $key = getMinisum($posibleData);
    $link[$key] = $posibleData[$key];

    list($x, $y) = explode('_', $key);
    $data_origin[$y][$x] = $link[$key][0];
}

echo '最终结果：' . PHP_EOL;
print_data($data_origin);

$timer->stop();
echo "执行该脚本用时:".$timer->spent(). '秒' . PHP_EOL;
echo "循环次数：" . $i . PHP_EOL;





function print_data($datas)
{
    foreach ($datas as $y=>$data){
        foreach ($data as $x=>$item){
            if(empty($item)){
                echo '[ ]';
            }else{
                echo ' ' . $item . ' ';
            }
            if(($x+1)%3==0) echo '|';
        }
        echo PHP_EOL;
        if(($y+1)%3==0) {
            echo '------------------------------';
            echo PHP_EOL;
        }
    }
    return true;
}

function getZone($x, $y)
{
    if($y<=2){
        $y=0;
    }elseif($y<=5){
        $y=3;
    }else{
        $y=6;
    }
    if($x<=2){
        $x=0;
    }elseif($x<=5){
        $x=1;
    }else{
        $x=2;
    }

    return $x+$y;
}

function getPosableData($x, $y)
{
    global $data_row, $data_col, $data_zone;
    $elements = [1,2,3,4,5,6,7,8,9];

    $key = getZone($x, $y);
    $tmp = array_unique(array_merge($data_row[$y], $data_col[$x], $data_zone[$key]));
    $data = array_values(array_diff($elements, $tmp));

    return $data;
}

// 获取最少可能的键
function getMinisum(&$posibleData)
{
    $tmpKey = $tmpSize = null;
    foreach ($posibleData as $key=>$value){
        if(is_null($tmpKey)){
            $tmpKey = $key;
            $tmpSize = count($value);
        }else{
            if(count($value)<$tmpSize){
                $tmpKey = $key;
                $tmpSize = count($value);
            }
        }
    }

    return $tmpKey;
}

function getReKey(&$link)
{
    if(empty($link)) {
        return;
    }

    global $reKeys;
    $tmp = array_reverse($link);
    $key = key($tmp);

    array_shift($link[$key]);
    if(empty($link[$key])){
        unset($link[$key]);
        getReKey($link);
        $reKeys[$key] = 0;
    }else{
        $data = $link[$key][0];
        $reKeys[$key] = $data;
    }
}


class Timer{
    private $startTime = 0;
    private $stopTime = 0;

    function start(){
        $this->startTime = microtime(true);
    }

    function stop(){
        $this->stopTime = microtime(true);
    }

    function spent(){
        return round(($this->stopTime-$this->startTime),4);
    }
}




