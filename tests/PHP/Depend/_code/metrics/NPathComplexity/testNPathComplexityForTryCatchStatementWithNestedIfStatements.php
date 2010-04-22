<?php
class testNPathComplexityForTryCatchStatementWithNestedIfStatements
{
    function testNPathComplexityForTryCatchStatementWithNestedIfStatements()
    {
        try {
            if (true) {}
        } catch (E1 $e) {
            if (false) {
            } else {
                if (true) {}
            }
        }
    }
}