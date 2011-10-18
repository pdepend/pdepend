<?php
function testFunctionPostfixGraphForInvocationWithMemberPrimaryPrefixProperty($i = 0)
{
    if ($i === 0) {
        testFunctionPostfixGraphForInvocationWithMemberPrimaryPrefixProperty(1)->bar;
    }
    return new stdClass();
}
