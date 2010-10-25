<?php
function testParserNotHandlesDoubleQuoteStringWithVariableAndEqualAsAssignment()
{
    "$foo=$bar";
}
