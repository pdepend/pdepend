<?php
function testParserHandlesVariableVariableAsForeachValue($array, $name)
{
    foreach ($array as $$name) {
        var_dump($value);
    }
}
testParserHandlesVariableVariableAsForeachValue(array(1,2,3), 'value');