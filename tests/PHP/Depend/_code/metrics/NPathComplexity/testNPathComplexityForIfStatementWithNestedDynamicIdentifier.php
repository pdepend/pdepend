<?php
class testNPathComplexityForIfStatementWithNestedDynamicIdentifier
{
    function testNPathComplexityForIfStatementWithNestedDynamicIdentifier()
    {
        if (self::${$var}) {
        }
    }
}