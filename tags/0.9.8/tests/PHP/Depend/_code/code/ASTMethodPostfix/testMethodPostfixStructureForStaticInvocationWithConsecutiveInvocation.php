<?php
function testMethodPostfixStructureForStaticInvocationWithConsecutiveInvocation()
{
    Bar::baz()->foo();
}