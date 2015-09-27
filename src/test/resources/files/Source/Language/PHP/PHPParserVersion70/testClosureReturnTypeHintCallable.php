<?php
function testClosureReturnTypeHintCallable() {
    $x = function() : callable {
        return function() {};
    };

    $x();
}
