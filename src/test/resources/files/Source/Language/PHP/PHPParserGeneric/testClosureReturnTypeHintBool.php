<?php
function testClosureReturnTypeHintBool()
{
    $x = function() : bool {
        return true;
    };

    $x();
}
