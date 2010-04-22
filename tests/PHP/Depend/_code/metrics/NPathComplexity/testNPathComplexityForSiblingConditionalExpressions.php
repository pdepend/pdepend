<?php
function testNPathComplexityForSiblingConditionalExpressions()
{
    $a = ($foo ? $bar : $baz);
    $b = $x ? $y : $z;
}