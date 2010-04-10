<?php
function testCatchStatementHasExpectedEndLine()
{
    try {
        throw Exception();
    } catch (Exception $e) {}
}