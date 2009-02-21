<?php
function pdepend($object) {
    try {
        $object->foo();
    } catch (OutOfBoundsException $e) {
    }
}