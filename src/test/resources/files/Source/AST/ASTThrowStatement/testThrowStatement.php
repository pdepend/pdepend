<?php
function testThrowStatement($bar, $object)
{
    throw new \RuntimeException(
        "Foo" . $bar . $object->baz );
}

testThrowStatement("23", (object) array("baz" => 42));
