<?php
function testFunctionPostfixGraphForArrayIndexedVariableInvocation($callbacks, $index)
{
    return $callbacks[$index]['name']();
}
