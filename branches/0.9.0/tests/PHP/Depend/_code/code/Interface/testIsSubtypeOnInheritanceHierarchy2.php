<?php
interface A extends D, E {}
interface B extends F {}
interface C extends B, A {}
interface D {}
interface E {}
interface F {}
?>
