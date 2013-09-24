<?php
function testReturnsByReferenceReturnsFalseByDefaultForStaticClosure()
{
    return static function ($x, $y) {
        return pow($x, $y);
    };
}
