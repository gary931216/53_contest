<?php
while(($line = readline()) !== false) { 
    $array = explode(" ",$line);
    $num = eval("return $line;");
    // 判斷括號
    
    for($i = 0; $i < count($array); $i ++)  {
        if($array[$i] == "(") {
            $left = $i;
            $right = rightparenthesis($array);
            $a = array_splice($array, $left, $right - $left + 1);
            array_shift($a);
            array_pop($a);
            array_splice($array, $left, 0, implode(" ", deal($a)));
        }
    }
    $array = deal($array);
    print_r($array);
    
}

function deal($array) {
    // 處理乘除
    for($i = 0; $i < count($array); $i ++) {
        if($array[$i] == "*" || $array[$i] == "/") {
            $a = array_splice($array, $i, 2);
            array_splice($array, $i, 0, ($a[1] . $a[0]));
            $i ++;
        }
    }
    // 處理加減
    for($i = 0; $i < count($array); $i ++) {
        if($array[$i] == "+" || $array[$i] == "-") {
            $a = array_splice($array, $i, 2);
            array_splice($array, $i, 0, ($a[1] . $a[0]));
            $i ++;
        }
    }
    return $array;
}

// 找右括弧
function rightparenthesis($array) {
    for($i = count($array) - 1; $i >= 0; $i --) {
        if($array[$i] == ")") {
            return $i;
        }
    }
}