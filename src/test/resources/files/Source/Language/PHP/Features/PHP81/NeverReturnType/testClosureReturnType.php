<?php
function testClosureReturnType() {
    $x = function() : never {
        exit;
    };

    $x();
}
