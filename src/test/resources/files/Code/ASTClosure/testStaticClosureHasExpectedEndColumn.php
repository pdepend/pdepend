<?php
function testStaticClosureHasExpectedEndColumn()
{
    return static
        function($x, $y) {
            return ($x * $y);
        };
}
