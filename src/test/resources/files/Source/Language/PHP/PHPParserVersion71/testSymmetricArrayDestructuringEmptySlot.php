<?php
function testSymmetricArrayDestructuringEmptySlot()
{
    [, $b] = ["a", "b"];
    var_dump($b);
}
