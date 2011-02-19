<?php
function testCalculatesExpectedLLocForIfAndElseIfStatement($i)
{
    if ($i > 42) {
        echo "NO";
    } else if ($i < 42) {
        echo "NO";
    } else {
        echo "YES";
    }
}