<?php
function testAssignmentExpressionFromFunctionReturnValue()
{
    $foo = bar()->baz;
}