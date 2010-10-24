<?php
function testFunctionPostfixStructureWithMemberPrimaryPrefixMethod($i = 0)
{
    if ($i === 0) {
        testFunctionPostfixStructureWithMemberPrimaryPrefixMethod(1)->bar();
    }
    return new stdClass();
}