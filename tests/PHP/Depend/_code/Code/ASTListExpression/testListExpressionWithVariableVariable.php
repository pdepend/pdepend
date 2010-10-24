<?php
function testListExpressionWithVariableVariable()
{
    list($$foo, $$$bar, $$$$baz['foo']) = func_get_args();
}