<?php
interface I {}

class A extends B {} // DIT = 4
class B extends D {} // DIT = 3
class C extends D {} // DIT = 3
class D extends E {} // DIT = 2
class E extends F {} // DIT = 1
class F implements I {} // DIT = 0
?>
