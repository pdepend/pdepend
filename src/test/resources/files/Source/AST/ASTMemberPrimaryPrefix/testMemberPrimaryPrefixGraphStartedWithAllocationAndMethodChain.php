<?php
function testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodChain()
{
    return (new MyClass)->foo()->bar();
}
