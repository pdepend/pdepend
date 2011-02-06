<?php
function testClosureHasExpectedEndColumn()
{
    return function($a) {
        return 52;
    };
}