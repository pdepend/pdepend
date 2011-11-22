<?php
function testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodInvocation()
{
    return (new MyClass())->foo();
}
