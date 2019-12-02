<?php
function testListExpressionWithKeys()
{
    list("a" => $a, "b" => $b) = array("a" => "a", "b" => "b");
    var_dump( $a, $b );
}
