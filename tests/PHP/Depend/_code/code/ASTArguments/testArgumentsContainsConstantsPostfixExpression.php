<?php
function testArgumentsContainsConstantsPostfixExpression()
{
    Foo::foo(\b\a\r\Bar::BAZ);
}