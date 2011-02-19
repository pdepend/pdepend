<?php
function foo()
{
    $x = function&($y) {
        return $y;
    };
    var_dump($x(42));
}
foo();