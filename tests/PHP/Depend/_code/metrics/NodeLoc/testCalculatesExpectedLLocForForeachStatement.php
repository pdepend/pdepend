<?php
function testCalculatesExpectedLLocForForeachStatement($array)
{
    foreach ($array as $key => $value) {
        echo "Key: ", $key, "; Value: ", $value, PHP_EOL;
    }
}