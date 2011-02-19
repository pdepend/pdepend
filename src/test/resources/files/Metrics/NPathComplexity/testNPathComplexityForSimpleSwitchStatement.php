<?php
class testNPathComplexityForSimpleSwitchStatement
{
    function testNPathComplexityForSimpleSwitchStatement()
    {
        switch (true) {
            case 1:
                ++$i;
                break;
        }
    }
}