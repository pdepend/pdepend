<?php
class A implements D, E {}
class B extends A {}
class C extends B implements F {}

interface D {}
interface E {}
interface F {}
?>
