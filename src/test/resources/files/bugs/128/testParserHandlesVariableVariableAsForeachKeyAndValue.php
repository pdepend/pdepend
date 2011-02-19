<?php
function testParserHandlesVariableVariableAsForeachKeyAndValue($array, $name1, $name2)
{
    foreach ($array as $$name1 => $$name2) {
        var_dump($key, $value);
    }
}
testParserHandlesVariableVariableAsForeachKeyAndValue(array(1,2,3), 'key', 'value');
