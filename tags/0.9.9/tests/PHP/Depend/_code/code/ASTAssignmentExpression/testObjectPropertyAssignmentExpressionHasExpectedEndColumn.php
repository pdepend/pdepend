<?php
function testObjectPropertyAssignmentExpressionHasExpectedEndColumn()
{
    $foo->bar = 'Hello
    World!!!11';
}