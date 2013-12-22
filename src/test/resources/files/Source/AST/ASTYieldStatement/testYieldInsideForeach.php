<?php

function testYieldInsideForeach()
{
    foreach ($foo as $bar) {
        yield $foo;
    }
}
