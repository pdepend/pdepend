<?php
function testDoubleQuoteStringWithEmbeddedComplexBacktickExpression()
{
    return "Issue `$ticketNo`";
}