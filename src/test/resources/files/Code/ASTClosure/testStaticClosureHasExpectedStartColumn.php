<?php
function testStaticClosureHasExpectedStartColumn()
{
    return static
        function($x, $y) {
            return ($x * $y);
        };
}
