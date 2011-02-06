<?php
class testNPathComplexityForSwitchStatementWithComplexCaseStatements
{
    function testNPathComplexityForSwitchStatementWithComplexCaseStatements()
    {
        switch (a) {
            case 0:
            case 1:
                for (;;) {}
                break;

            case 2:
                do {} while (true);
                break;

            case 3:
                break;

            default:
                while (true) {}
                break;
        }
    }
}