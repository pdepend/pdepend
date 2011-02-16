<?php
class testNPathComplexityForSimpleDoWhileStatement
{
    function testNPathComplexityForSimpleDoWhileStatement()
    {
        do {
            echo ($a or $b);
        } while ($a and $b);
    }
}