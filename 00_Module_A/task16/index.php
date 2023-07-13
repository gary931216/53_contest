<?php
$factor = $_GET['factor'];
$array = [];
$num = 40;

di($num);

function di($num) {
    global $array, $factor;
    if($num > 0) {
        di($num - 1);
        echo $num;
    }else {
        print_r($array);
    }
    if($num % (int)$factor) {
        $array[$num - 1] = (string)$num . "is a multiple of " . (string)$factor . "**";
    }else {
        $array[$num - 1] = $num;
    }
}