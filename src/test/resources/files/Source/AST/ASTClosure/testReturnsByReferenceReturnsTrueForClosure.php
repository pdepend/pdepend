<?php
function testReturnsByReferenceReturnsTrueForClosure()
{
    return array_map(
        function &($a) {
            return $a;
        },
        array()
    );
}