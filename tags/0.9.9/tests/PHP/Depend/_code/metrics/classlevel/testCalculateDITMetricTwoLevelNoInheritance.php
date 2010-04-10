<?php
interface I {}

class A extends C {} // DIT = 2
class C extends B {} // DIT = 1
class B implements I {} // DIT = 0
?>
