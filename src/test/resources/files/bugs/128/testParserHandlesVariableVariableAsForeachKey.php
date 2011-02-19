<?php
function testParserHandlesVariableVariableAsForeachKey($array, $name)
{
    foreach ($array as $$name => $value) {
        var_dump($key);
    }
}
testParserHandlesVariableVariableAsForeachKey(array(1,2,3), 'key');
