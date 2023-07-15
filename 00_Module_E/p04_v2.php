<?php
$num = explode(' ', readline());
$primary = explode(' ', readline());
$type = readline();
$arrays = [];
$answer = null;
$flag = null;

for($i = 0; $i < $num[0]; $i ++) {
    $array = explode(' ', readline());
    $sum = 0;
    if($type == 1) {
        for($x = 0; $x < $num[1]; $x++) {
            $sum += pow($primary[$x] - $array[$x], 2);
        }
    }else {
        $sum = count(array_diff_assoc($array, $primary));
    }
    if($answer == null) {
        $answer = $i;
        $flag = $sum;
    }
    if($sum < $flag) {
        $answer = $i;
        $flag = $sum;
    }
    array_push($arrays, [
        'value' => $array,
        'sum' => $sum
    ]);
}

echo implode(" ", $arrays[$answer]['value']);
