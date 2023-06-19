<?php
while(($line = readline()) !== false) { 
    $num = explode(" ", $line)[0]; 
    $long = explode(" ", $line)[0];
    $v0 = explode(" ", readline());
    $method = readline();
    $vn = [];
    $answer;
    for($i = 0; $i < $num; $i ++) {
        array_push($vn, explode(" ", readline()));
    }
    if($method == 1) {
        $answer = ozzd($v0, $vn);
    }else {
        $answer = hanmean($v0, $vn);
    }
    for($z = 0; $z < count($answer); $z ++) {
        echo $answer[$z] . " ";
    }
}

function ozzd($v0, $vn) {
    $flag = false;
    $return;
    for($z = 0; $z < count($vn); $z ++) {
        // 取出Vn
        $distant = 0;
        for($i = 0; $i < count($v0); $i ++) {
            // 取出V0有幾個
            $distant += pow(($v0[$i] - $vn[$z][$i]), 2);
        }
        $distant = sqrt($distant);
        if($flag == false || $flag > $distant) {
            $flag = $distant;
            $return = $vn[$z];
        }
    }
    return $return;
}
function hanmean($v0, $vn) {
    $flag = false;
    $return;
    for($z = 0; $z < count($vn); $z ++) {
        // 取出Vn
        $distant = 0;
        for($i = 0; $i < count($v0); $i ++) {
            // 取出V0有幾個
            $v0[$i] = (String)$v0[$i];
            $vn[$z][$i] = (String)$vn[$z][$i];
            
            if($v0[$i] != $vn[$z][$i]) {
                $distant ++;
            }
        }
        if($flag == false || $flag > $distant) {
            $flag = $distant;
            $return = $vn[$z];
        }
    }
    return $return;
}