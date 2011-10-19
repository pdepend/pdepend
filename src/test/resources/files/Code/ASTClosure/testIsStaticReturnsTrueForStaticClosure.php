<?php
function testIsStaticReturnsTrueForStaticClosure()
{
    $closure = static function($x, $y) {
        return pow($x, $y);
    };
    var_dump($closure(2, 2));
}
testIsStaticReturnsTrueForStaticClosure();
