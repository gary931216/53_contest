<?php
$line = readline();
$answer = eval("return $line;");
$lineAnswer = '';
$stack = [];
$array = explode(" ", $line);

for($i = 0; $i < count($array); $i ++) {
    if($array[$i] == "(") {
        array_push($stack, $array[$i]);
    }else if($array[$i] == ")") {
        while(count($stack) != 0) {
            $pop = array_pop($stack);
            if($pop == '(') {
                break;
            }
            $lineAnswer .= $pop . " ";
        }
    }else if($array[$i] == "+" || $array[$i] == "-") {
        while(count($stack) != 0) {
            $pop = array_pop($stack);
            if($pop == '(') {
                array_push($stack, $pop);
                break;
            }
            $lineAnswer .= $pop . " ";
        }
        array_push($stack, $array[$i]);
    }else if($array[$i] == "*" ||$array[$i] == "/") {
        while(count($stack) != 0) {
            $pop = array_pop($stack);
            if($pop == '(' || $pop == "+" || $pop == "-") {
                array_push($stack, $pop);
                break;
            }
            $lineAnswer .= $pop . " ";
        }
        array_push($stack, $array[$i]);
    }else {
        $lineAnswer .= $array[$i] . " ";
    }
}
while(count($stack) != 0) {
    $pop = array_pop($stack);
    $lineAnswer .= $pop . " ";
}
echo $lineAnswer . "\n" . $answer;