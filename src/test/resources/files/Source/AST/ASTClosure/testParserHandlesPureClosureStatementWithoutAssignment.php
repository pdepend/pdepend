<?php
function testParserHandlesPureClosureStatementWithoutAssignment()
{
    function($foo) {
        echo $foo;
    };
}
