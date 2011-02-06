<?php
interface A {}
interface B extends F {}
interface C extends B, D {}
interface D extends A, E {}
interface E {}
interface F {}
?>
