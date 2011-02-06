<?php
function testClosureHasExpectedStartLine()
{
    return function($a) {
        return 52;
    };
}