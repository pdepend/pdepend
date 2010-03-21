<?php
function testUnsetStatementHasExpectedStartColumn($bar, $object)
{
    unset(
            $bar,
            $object );
}

testUnsetStatementHasExpectedEndColumn("23", (object) array("baz" => 42));