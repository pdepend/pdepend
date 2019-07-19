<?php

function testParenthesisAroundCallableParsesArguments()
{
    $callback(1, 2);

    ($object->callback)(1, 2);
}
