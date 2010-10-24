<?php
function testCatchStatementVariableHasExpectedStartLine()
{
    try {
        x();
    } catch (
        Exception
        $e
    ) {}
}