<?php
function testForeachStatementWithObjectPropertyAsValue( $message, $object )
{
    foreach ( $message as $object->value )
    {
    }
}