<?php
function testCalculatesExpectedLLocForDoWhileStatement()
{
    $i = 42;
    do {
        --$i;
    } while ($i > 0);
}