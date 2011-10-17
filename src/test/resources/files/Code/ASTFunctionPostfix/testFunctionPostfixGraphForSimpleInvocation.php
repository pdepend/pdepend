<?php
function testFunctionPostfixGraphForSimpleInvocation($i = 0)
{
    if ($i === 0) {
        testFunctionPostfixGraphForSimpleInvocation(1);
    }
}
