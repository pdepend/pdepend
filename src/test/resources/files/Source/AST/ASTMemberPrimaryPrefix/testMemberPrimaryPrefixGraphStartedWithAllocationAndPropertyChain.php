<?php
function testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyChain()
{
    return (new MyClass)->foo->bar;
}
