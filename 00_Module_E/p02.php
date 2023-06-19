<?php
while(($line = readline()) !== false) { 
    echo f($line);
}

function f($num) {
    if($num == 0 || $num == 1) {
        return $num;
    }
    return f($num - 1) + f($num - 2);
}