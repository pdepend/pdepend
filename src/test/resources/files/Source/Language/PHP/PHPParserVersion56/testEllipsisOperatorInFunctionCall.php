<?php
function foo($a, $b) {
}

function bar($x, $y) {
    foo(...[$x, $y]);
}

function baz() {
    bar(42, ...[23]);
}
