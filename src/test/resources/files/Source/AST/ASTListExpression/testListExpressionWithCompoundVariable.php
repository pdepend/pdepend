<?php
function testListExpressionWithCompoundVariable()
{
    list(${foo}, $bar, $baz) = func_get_args();
}