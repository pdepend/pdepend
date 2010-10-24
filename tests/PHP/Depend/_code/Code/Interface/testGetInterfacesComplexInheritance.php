<?php
interface A extends B, E {}

interface B extends D, C {}
interface C extends D, E, F {}
interface D extends E {}
interface E extends F {}
interface F {}
?>
