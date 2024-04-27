<?php
function testClosureReturnTypeHintClass() {
    $x = function() : \Iterator {
        return new EmptyIterator();
    };

    $x();
}
