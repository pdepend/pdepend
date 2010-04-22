<?php
function testNPathComplexityForSiblingExpressions()
{
    $x = $y || $z;
    $x = $x ?: $foo;
    //$x = $foo && $x or $bar;
    return $fx or $fy xor $fz and $ff;
}