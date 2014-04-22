<?php
function input_parser($input) {
    foreach (explode("\n", $input) as $id => $line) {
        yield $id => $line;
    }
}
