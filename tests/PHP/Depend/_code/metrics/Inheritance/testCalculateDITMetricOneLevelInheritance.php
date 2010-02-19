<?php
interface I {}

class A extends B {}    // DIT = 1
class B implements I {} // DIT = 0
?>
