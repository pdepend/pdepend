<?php
function testReturnsByReferenceReturnsTrueForAssignedClosure()
{
    $closure = function &($a) {
        return (23 + ($a * 42));
    };
}