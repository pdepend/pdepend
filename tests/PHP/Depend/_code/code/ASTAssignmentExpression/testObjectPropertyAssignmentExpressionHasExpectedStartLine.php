<?php
function testObjectPropertyAssignmentExpressionHasExpectedStartLine()
{
    $foo->bar = 'Hello
    World!!!11';
}