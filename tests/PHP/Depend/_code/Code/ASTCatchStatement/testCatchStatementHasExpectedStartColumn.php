<?php
function testCatchStatementHasExpectedStartColumn()
{
    try {
        throw Exception();
    } catch (Exception $e) {}
}