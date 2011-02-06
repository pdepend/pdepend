<?php
function testBacktickExpressionWithEmbeddedComplexDoubleQuoteString()
{
    $foo = `Issue "$ticketNo"`;
}