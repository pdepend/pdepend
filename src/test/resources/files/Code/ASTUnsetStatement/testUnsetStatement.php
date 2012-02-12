<?php
function testUnsetStatement($bar, $object)
{
    unset(
            $bar,
            $object );
}

testUnsetStatement("23", (object) array("baz" => 42));
