<?php
function testTryStatementHasExpectedStartLine()
{
    try {
        fooBar();
    } catch (Exception $e) {

    }
}