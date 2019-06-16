<?php
/**
 * Created by PhpStorm.
 * User: zhuang
 * Date: 2017/3/22
 * Time: 19:50
 */


echo '开始内存：'.memory_get_usage() .  PHP_EOL;
$point = '1,1';// 起点
$obstacle = [// 阻碍点
    //'2,1',
];
$width = 5;
$high = 5;

$link = [];
while(1) {
    list($x, $y) = explode(',', $point);
    $valid = getVaildPoint($point, $width, $high, $obstacle, $link);
    isset($link[$point]) && $valid |= $link[$point];
    $link[$point] = $valid;
    if(count($link)==($width*$high-count($obstacle))) {
        printResult($link);
    }

    if($valid==15){
        array_pop($link);
        if(empty($link)) break;
        end($link);
        $point = key($link);
        reset($link);
        continue;
    }

    for ($i = 0; $i < 4; $i++) {
        $tmp = pow(2, $i);
        if ($tmp == ($tmp & $valid)) {
            continue;
        }
        // 对来源做标记
        $valid |= $tmp;
        $link[$point] = $valid;
        // 移动到下个点
        switch($tmp){
            case 1:
                $y--;
                break;
            case 2:
                $y++;
                break;
            case 4:
                $x--;
                break;
            case 8:
                $x++;
                break;
            default:
                break;
        }
        $point = $x . ',' . $y;
        break;
    }
}
echo '运行后内存：'.memory_get_usage(). PHP_EOL;


// 获取四周可用的点
// 1-上 2-下 4-左 8-右
function getVaildPoint($point, $width, $high, &$obstacle=null, &$history=null)
{
    $validDirection = 0;
    // 排除边界
    list($x, $y) = explode(',', $point);
    if($x==1){
        $validDirection |= 4;
    }elseif ($x==$width){
        $validDirection |= 8;
    }
    if($y==1){
        $validDirection |= 1;
    }elseif ($y==$high){
        $validDirection |= 2;
    }
    // 排除阻碍点
    if($obstacle){
        $tmpPoint = ($x-1) . ',' . $y;
        if(in_array($tmpPoint, $obstacle)){
            $validDirection |= 4;
        }
        $tmpPoint = ($x+1) . ',' . $y;
        if(in_array($tmpPoint, $obstacle)){
            $validDirection |= 8;
        }
        $tmpPoint = $x . ',' . ($y-1);
        if(in_array($tmpPoint, $obstacle)){
            $validDirection |= 1;
        }
        $tmpPoint = $x . ',' . ($y+1);
        if(in_array($tmpPoint, $obstacle)) {
            $validDirection |= 2;
        }
    }
    // 排除来源
    if($history) {
        foreach ($history as $key=>$item) {
            list($f_x, $f_y) = explode(',', $key);
            if ($f_x == $x) {
                if ($f_y-$y==1) {
                    $validDirection |= 2;
                } elseif($y-$f_y==1) {
                    $validDirection |= 1;
                }
            } elseif ($f_y == $y) {
                if ($f_x-$x==1) {
                    $validDirection |= 8;
                } elseif($x-$f_x==1) {
                    $validDirection |= 4;
                }
            }
        }
    }

    return $validDirection;
}

// 打印结果
function printResult(&$link)
{
    $i = 1;
    foreach ($link as $key=>$val){
        if($i==1){
            echo $key;
        }else{
            echo '->' . $key;
        }
        if($i%10==0) echo PHP_EOL;
        $i++;
    }
    echo PHP_EOL;
    echo PHP_EOL;
}

