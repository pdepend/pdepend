<?php
function testFunctionPostfixGraphForInvocationWithMemberPrimaryPrefixMethod($i = 0)
{
    if ($i === 0) {
        testFunctionPostfixGraphForInvocationWithMemberPrimaryPrefixMethod(1)->bar();
    }
    return new stdClass();
}
