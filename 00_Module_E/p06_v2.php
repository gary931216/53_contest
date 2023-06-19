<?php
while(($line = readline()) !== false) { 
    $array = explode(" ",$line);
    $num = eval("return $line;");
    $stack = [];
    $answer = "";
    for($i = 0; $i < count($array); $i ++)  {
        if($array[$i] == "(") {
            array_push($stack, "(");
        }elseif($array[$i] == ")") {
            $char = array_pop($stack);
            while($char != "(") {
                $answer .= $char . " ";
                $char = array_pop($stack);
            }
        }elseif($array[$i] == "+" || $array[$i] == "-") {
            while(count($stack) != 0) {
                $char = array_pop($stack);
                if($char == "(") {
                    array_push($stack, $char);
                    break;
                }
                $answer .= $char . " ";
            }
            array_push($stack, $array[$i]);
        }elseif($array[$i] == "*" || $array[$i] == "/") {
            $char = array_pop($stack);
            if($char == "+" || $char == "-" || $char == "(") {
                array_push($stack, $char);
            }else {
                while($char != "+" || $char != "-") {
                    if($char == "(") {
                        array_push($stack, $char);
                        break;
                    }
                    $answer .= $char . " ";
                    $char = array_pop($stack);
                }
            }
            array_push($stack, $array[$i]);
        }else {
            $answer .= $array[$i] . " ";
        }
    }
    while(count($stack) != 0) {
        $char = array_pop($stack);
        $answer .= $char . " ";
    }
    echo $answer;
    echo "\n";
    echo $num;
}