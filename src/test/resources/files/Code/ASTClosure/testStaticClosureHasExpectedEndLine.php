<?php
function testStaticClosureHasExpectedEndLine()
{
    return static
        function($x, $y) {
            return ($x * $y);
        };
}
