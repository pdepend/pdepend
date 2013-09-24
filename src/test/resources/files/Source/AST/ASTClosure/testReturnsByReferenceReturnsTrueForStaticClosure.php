<?php
function testReturnsByReferenceReturnsTrueForStaticClosure()
{
    return static function &($x, $y) {
        return pow($x, $y);
    };
}
