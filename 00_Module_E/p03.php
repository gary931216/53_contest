<?php
while(($line = readline()) != false) {
    $array = [];
    $answer = [];
    for($i = 0; $i < $line; $i ++) {
        array_push($array, readline());
        array_push($answer, []);
    }
    for($i = 0; $i < $line; $i ++) {
        for($z = 2; $z <= $array[$i]; $z ++) {
            if($array[$i] % $z != 0) {
                continue;
            }
            $num  = $z;
            $time = 0;
            while($array[$i] % $z == 0) {
                $array[$i] /= $z;
                $time ++;
            }
            array_push($answer[$i], [
                "num" => $num,
                "time" => $time
            ]);
        } 
    }
    for($i = 0; $i < $line; $i ++) {
        $word = "";
        for($z = 0; $z < count($answer[$i]); $z ++) {
            if($answer[$i][$z]["time"] == 1) {
                $word .= $answer[$i][$z]["num"];
            }else {
                $word .= $answer[$i][$z]["num"] . "^" . $answer[$i][$z]["time"];
            }
            if($z != count($answer[$i]) - 1) {
                $word .= "*";
            }
        }
        echo $word . "\n";
    }
}