<?php
function gen() {
    yield from from(); // keys 0-2
}
function from() {
    yield 1; // key 0
    yield 2; // key 1
    yield 3; // key 2
}
// pass false as second parameter to get an array [0, 1, 2, 3, 4]
var_dump(iterator_to_array(gen()));
