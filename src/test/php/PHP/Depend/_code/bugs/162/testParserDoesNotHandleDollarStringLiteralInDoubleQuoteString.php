<?php
function testParserDoesNotHandleDollarStringLiteralInDoubleQuoteString($token)
{
    return "$token$";
}