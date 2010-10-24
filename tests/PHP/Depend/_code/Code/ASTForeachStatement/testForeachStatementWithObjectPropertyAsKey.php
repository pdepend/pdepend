<?php
function testForeachStatementWithObjectPropertyAsKey( $message, $object )
{
    foreach ( $message as $object->foo => $value )
    {
    }
}