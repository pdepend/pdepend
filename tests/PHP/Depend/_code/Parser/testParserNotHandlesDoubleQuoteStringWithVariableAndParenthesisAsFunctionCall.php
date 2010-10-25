<?php
function testParserNotHandlesVariableWithFollowingParenthesisAsFunctionCall()
{
    "$foo(42);";
}
