<?php
function test(?string $name)
{
    var_dump($name);
}

test('tpunt'); // string(5) "tpunt"
test(null); // NULL
test(); // Uncaught Error: Too few arguments to function test(), 0 passed in...
