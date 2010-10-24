<?php
function testParserThrowsExceptionWhenNoCatchStatementFollows()
{
    try {
        fooBar();
    }
}