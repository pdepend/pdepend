<?php
class testNPathComplexityForConsecutiveIfStatements
{
    function testNPathComplexityForConsecutiveIfStatements()
    {
        if (true) {
        }
        if (true) {
        }
        if (true) {
        }
        if (true) {
        }
        if (true && true && true && true) {
        }
    }
}