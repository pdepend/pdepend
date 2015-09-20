<?php
function testClosureReturnTypeHintString()
{
    $x = function() : string {
        return 'foobar';
    };

    $x();
}
