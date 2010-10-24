<?php
function testForeachStatementWithKeyAndObjectPropertyByReference( array $message, $object )
{
    foreach ( $message as $key => &$object )
    {
    }
}