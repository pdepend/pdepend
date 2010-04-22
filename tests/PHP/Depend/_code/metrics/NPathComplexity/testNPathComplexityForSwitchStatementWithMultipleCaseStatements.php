<?php
class testNPathComplexityForSwitchStatementWithMultipleCaseStatements
{
    function testNPathComplexityForSwitchStatementWithMultipleCaseStatements()
    {
        switch (true) {
            case 1:
            case 2:
                ++$i;
                break;

            case 3:
            case 4:
            case 5:
                --$i;
                break;
        }
    }
}