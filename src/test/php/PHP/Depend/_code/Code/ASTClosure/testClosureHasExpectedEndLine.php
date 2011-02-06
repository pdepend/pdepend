<?php
function testClosureHasExpectedEndLine()
{
    return function($a) {
        return 52;
    };
}