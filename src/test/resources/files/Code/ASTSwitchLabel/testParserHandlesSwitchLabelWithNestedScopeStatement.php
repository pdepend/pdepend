<?php
function testParserHandlesSwitchLabelWithNestedScopeStatement()
{
    switch ($foo) {
    
        default: {
            echo "HELLO WORLD";
        }
        break;
    }
}

testParserHandlesSwitchLabelWithNestedScopeStatement();