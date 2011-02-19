<?php
function testCalculatesExpectedLLocForWhileStatement($param)
{
    while ($param < 42) {
        ++$param;
    }
}