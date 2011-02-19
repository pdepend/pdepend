<?php
function testArgumentsContainsPropertyPostfixExpression()
{
    Foo::bar(Bar::$baz);
}