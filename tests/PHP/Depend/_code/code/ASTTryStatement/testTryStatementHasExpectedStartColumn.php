<?php
function testTryStatementHasExpectedStartColumn()
{
    try {
        fooBar();
    } catch (Exception $e) {

    }
}