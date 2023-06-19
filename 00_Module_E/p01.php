<?php
while(($line = readline()) !== false) { 
    $array = [];
    $flag = 0;
    for($i = 0; $i < $line; $i ++) {
        array_push($array, readline());
    }
    for($i = 0; $i < $line; $i ++) {
        $circle = 0;
        $cross = 0;
        for($z = 0; $z < $line; $z ++) {
            if($array[$i][$z] == "O") {
                $circle ++;
            }else {
                $cross ++;
            }
        }
        if($circle == $line) {
            echo "O";
            $flag = 1;
            break;
        }elseif($cross == $line) {
            echo "X";
            $flag = 1;
            break;
        }
        $circle = 0;
        $cross = 0;
        for($z = 0; $z < $line; $z ++) {
            if($array[$z][$i] == "O") {
                $circle ++;
            }else {
                $cross ++;
            }
        }
        if($circle == $line) {
            echo "O";
            $flag = 1;
            break;
        }elseif($cross == $line) {
            echo "X";
            $flag = 1;
            break;
        }
    }
    if($flag == 0) {
        $now = $array[0][0];
        $time = 1;
        for($i = 1; $i < $line; $i ++) {
            if($array[$i][$i] != $now) {
                break;
            }else {
                $time ++;
            }
        }
        if($time == $line) {
            echo $now;
            $flag = 1;
        }
    }
    if($flag == 0) {
        $now = $array[0][$line - 1];
        $time = 1;
        for($i = 1; $i < $line; $i ++) {
            if($array[$line - $i - 1][$i] != $now) {
                break;
            }else {
                $time ++;
            }
        }
        if($time == $line) {
            echo $now;
            $flag = 1;
        }
    }
    if($flag == 0) {
        echo "?";
    }
} 