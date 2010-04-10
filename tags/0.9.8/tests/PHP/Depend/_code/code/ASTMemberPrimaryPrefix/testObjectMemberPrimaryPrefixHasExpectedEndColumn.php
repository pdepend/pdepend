<?php
function testObjectMemberPrimaryPrefixHasExpectedEndColumn()
{
    $foo->foo(
        __FUNCTION__
    )->bar = 42;
}