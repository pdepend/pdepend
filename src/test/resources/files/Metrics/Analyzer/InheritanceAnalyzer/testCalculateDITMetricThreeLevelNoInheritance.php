<?php
interface I {}

class A extends C {} // DIT = 3
class B extends C {} // DIT = 3
class C extends E {} // DIT = 2
class E extends D {} // DIT = 1
class D implements I {} // DIT = 0
?>
