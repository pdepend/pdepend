<?php
function testClosureHasExpectedStartColumn()
{
    return function($a) {
        return 52;
    };
}