<?php
function testTryStatementHasExpectedEndColumn()
{
    try {
        fooBar();
    } catch (Exception $e) {

    }
}