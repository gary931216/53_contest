<?php
while(($line = readline()) !== false) { 
    $array = [50, 10, 5, 1];
    $answer = [];
    $sum = 0;
    for($i = 0; $i < count($array); $i ++) {
        $time = floor($line / $array[$i]);
        $line -= $array[$i] * $time;
        array_push($answer, [
            "num" => $array[$i],
            "time" => $time
        ]);
        $sum += $time;
    }
    for($i = count($answer) - 1; $i >= 0 ; $i --) {
        echo $answer[$i]["num"] . " " . $answer[$i]["time"] . "\n";
    }
    echo $sum;
}