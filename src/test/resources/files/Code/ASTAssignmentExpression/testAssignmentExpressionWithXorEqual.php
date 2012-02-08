<?php
function testAssignmentExpressionWithXorEqual( &$foo )
{
    $foo ^= (1|2|8);
}
