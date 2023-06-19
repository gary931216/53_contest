<?php
while(($line = readline()) !== false) { 
    $start = explode(" ", $line)[0];
    $stop = explode(" ", $line)[1];
    $num = readline();
    $table = [];
    for($i = 0; $i < $num; $i ++) {
        array_push($table, readline());
    }
    if(!in_array($stop, $table)) {
        echo 0;
        exit;
    }
    array_unshift($table, $start);
    $can = [];
    for($i = 0; $i < count($table); $i ++) {
        $nowcan = [];
        for($z = 0; $z < count($table); $z ++) {
            $different = 0;
            for($x = 0; $x < strlen($table[$i]); $x ++) {
                if($table[$i][$x] != $table[$z][$x]) {
                    $different ++;
                }
            }
            if($different == 1) {
                array_push($nowcan, true);
            }else {
                array_push($nowcan, false);
            }
        }
        array_push($can, $nowcan);
    }
    $fail = 0;
    $best;
    $current_index = 0;
    $long = [];
    for($i = 0; $i < count($table); $i ++) {
        if($i == 0) {
            $long[$i] = [
                "long" => 0,
                "best" => true,
            ];
        }else {
            $long[$i] = [
                "long" => -1,
                "best" => false,
            ];
        }
    }
    while(1) {
        for($i = 0; $i < count($can[$current_index]); $i ++) {
            if($can[$current_index][$i] && $long[$i]['best'] != true) {
                if($current_index == 0) {
                    $long[$i]['long'] = 1;
                }else{
                    if($long[$i]['long'] > $long[$current_index]['long'] + 1 || $long[$i]['long'] == -1) {
                        $long[$i]['long'] = $long[$current_index]['long'] + 1;
                    }
                }
            }
        }
        $nowbest = -1;
        for($i = 0; $i < count($long); $i ++) {
            // echo $long[$i]['long'] . "\n";
            if($nowbest == -1 && $long[$i]['long'] != -1 && $long[$i]['best'] != true) {
                $nowbest = $long[$i]['long'];
                $current_index = $i;
            }else if($nowbest > $long[$i]['long'] && $long[$i]['long'] != -1 && $long[$i]['best'] != true) {
                $nowbest = $long[$i]['long'];
                $current_index = $i;
            }
        }
        $long[$current_index]['best'] = true;
        echo $nowbest . "\n";
        print_r($long);
        if($nowbest == -1) {
            $fail = 1;
            break;
        }
        if($current_index == array_search($stop, $table)) {
            $best = $long[$current_index]["long"];
            break;
        }
    }
    if($fail) {
        echo 0;
    }else {
        echo $best;
    }
}