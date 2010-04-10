<?php
function testUnclosedArgumentsExpressionThrowsExpectedException()
{
    foo((string $bar);
}