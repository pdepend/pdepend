<?php
function testIsStaticReturnsFalseForNonStaticClosure()
{
    return function($x, $y) {
        return pow($x, $y);
    };
}
