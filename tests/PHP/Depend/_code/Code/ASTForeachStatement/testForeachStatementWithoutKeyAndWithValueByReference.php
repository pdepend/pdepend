<?php
function testForeachStatementWithoutKeyAndWithValueByReference()
{
    foreach ($expr as &$value) {}
}