<?php
function testChainedPropertyAssignmentExpressionHasExpectedEndColumn()
{
    Foo::bar(
        __FUNCTION__
    )->baz(
        __FILE__
    )->value = 'FOOBAR';
}