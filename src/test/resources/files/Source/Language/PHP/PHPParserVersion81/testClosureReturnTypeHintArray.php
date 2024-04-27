<?php
function testClosureReturnTypeHintArray() {
    $x = function() : array {
        return [42];
    };

    $x();
}
