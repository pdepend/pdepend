<?php
function testObjectMemberPrimaryPrefixHasExpectedEndLine()
{
    $foo->foo(
        __FUNCTION__
    )->bar = 42;
}