<?php
function testYieldValueAssignmentKeyValue()
{
    $x = 1 + (yield "key" => 2);
}
