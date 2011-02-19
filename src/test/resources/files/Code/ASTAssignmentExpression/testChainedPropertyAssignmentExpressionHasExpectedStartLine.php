<?php
function testChainedPropertyAssignmentExpressionHasExpectedStartLine()
{
    Foo::bar(
        __FUNCTION__
    )->baz(
        __FILE__
    )->value = 'FOOBAR';
}