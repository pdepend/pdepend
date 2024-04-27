<?php

function testParenthesisAroundCallableParsesArguments()
{
    (($object->callback)(1, 2))(1, 2);
}
