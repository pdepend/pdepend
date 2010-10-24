<?php
function testObjectMemberPrimaryPrefixHasExpectedStartColumn()
{
    $foo->foo(
        __FUNCTION__
    )->bar = 42;
}