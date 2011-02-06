<?php
function testArgumentsContainsAllocationExpression()
{
    Foo::bar(new Baz());
}