<?php
function testClosureReturnTypeHintFloat() {
    $x = function() : float {
        return 3.14;
    };

    $x();
}
