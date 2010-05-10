<?php
function testCalculatesExpectedLLocForForStatement()
{
    for ($i = 0; $i < 42; ++$i) {
        for ($j = 0; $j < 23; ++$j) {
            echo ($i * $j);
        }
    }
}