<?php
function testArgumentsContainsStaticMethodPostfixExpression()
{
    Foo::bar(Bar::baz());
}