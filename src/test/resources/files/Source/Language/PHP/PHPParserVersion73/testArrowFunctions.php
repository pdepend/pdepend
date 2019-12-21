<?php

function doubleAll(array $numbers) {
    return array_map(fn($number) => $number * 2, $numbers);
}
