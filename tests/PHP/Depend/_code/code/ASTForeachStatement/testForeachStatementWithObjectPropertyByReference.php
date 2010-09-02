<?php
function testForeachStatementWithObjectPropertyByReference( $object )
{
    foreach ( $message as &$object->foo )
    {
    }
}