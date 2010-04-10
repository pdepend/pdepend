<?php
function testObjectPropertyAssignmentExpressionHasExpectedStartColumn()
{
    $foo->bar = 'Hello
    World!!!11';
}