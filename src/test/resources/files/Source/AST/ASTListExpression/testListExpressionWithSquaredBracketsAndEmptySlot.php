<?php
function testListExpressionWithSquaredBracketsAndEmptySlot()
{
    [$a, , $c] = ["a", "b", "c"];
    var_dump($a, $c);
}
