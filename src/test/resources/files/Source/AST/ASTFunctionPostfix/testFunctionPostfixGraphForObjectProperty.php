<?php
function testFunctionPostfixGraphForObjectProperty($foo)
{
    return $foo->bar[0]();
}
