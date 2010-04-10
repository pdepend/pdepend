<?php
function testCatchStatementHasExpectedStartLine()
{
    try {
        throw Exception();
    } catch (Exception $e) {}
}