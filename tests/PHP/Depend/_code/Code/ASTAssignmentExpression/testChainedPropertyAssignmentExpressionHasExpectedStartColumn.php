<?php
function testChainedPropertyAssignmentExpressionHasExpectedStartColumn()
{
    Foo::bar(
        __FUNCTION__
    )->baz(
        __FILE__
    )->value = 'FOOBAR';
}