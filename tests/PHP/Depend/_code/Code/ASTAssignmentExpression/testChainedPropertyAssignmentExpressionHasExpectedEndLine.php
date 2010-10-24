<?php
function testChainedPropertyAssignmentExpressionHasExpectedEndLine()
{
    Foo::bar(
        __FUNCTION__
    )->baz(
        __FILE__
    )->value = 'FOOBAR';
}