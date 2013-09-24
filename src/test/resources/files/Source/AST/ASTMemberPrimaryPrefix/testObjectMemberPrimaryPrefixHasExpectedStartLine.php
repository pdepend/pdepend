<?php
function testObjectMemberPrimaryPrefixHasExpectedStartLine()
{
    $foo->foo(
        __FUNCTION__
    )->bar = 42;
}