<?php
function testCalculateCCNDetectsExpressionsInAForLoop()
{
    for ($i = 0; $i < 42 && true || false; ++$i) {

    }
}