<?php

function bar() {
    $a = true;
    return function () use ($a,) {};
}
