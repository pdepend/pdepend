<?php
function testCatchStatementHasExpectedEndColumn()
{
    try {
        throw Exception();
    } catch (Exception $e) {}
}