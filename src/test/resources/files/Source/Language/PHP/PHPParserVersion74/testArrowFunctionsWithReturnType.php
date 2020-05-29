<?php

function doubleAll(array $numbers): array {
    return array_map(fn($number): int => $number * 2, $numbers);
}
