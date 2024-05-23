<?php
function testClosureReturnTypeHintInt() {
    $x = function() : int {
        return 42;
    };

    $x();
}
