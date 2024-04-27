<?php

function destructure() {
    $array = [1, [2, 3]];
    [&$a, [$b, &$c]] = $array;
    $a = 4;
    $b = 5;
    $c = 6;

    return $array;
}
