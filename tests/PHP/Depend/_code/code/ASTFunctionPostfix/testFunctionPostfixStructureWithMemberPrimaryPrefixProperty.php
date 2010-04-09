<?php
function testFunctionPostfixStructureWithMemberPrimaryPrefixProperty($i = 0)
{
    if ($i === 0) {
        testFunctionPostfixStructureWithMemberPrimaryPrefixProperty(1)->bar;
    }
    return new stdClass();
}