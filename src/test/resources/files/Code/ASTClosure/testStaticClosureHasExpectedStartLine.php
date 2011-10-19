<?php
function testStaticClosureHasExpectedStartLine()
{
    return static
        function($x, $y) {
            return ($x * $y);
        };
}
