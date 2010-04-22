<?php
class testNPathComplexityForNestedWhileStatements
{
    function testNPathComplexityForNestedWhileStatements()
    {
        while (true || false) {
            while (true && false) {
                echo "'echo'";
            }
        }
    }
}