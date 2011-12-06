<?php
function testParserThrowsExpectedExceptionWhenIfStatementHasNoBody()
{
    if (time() % 42 === 0)
        //
}
