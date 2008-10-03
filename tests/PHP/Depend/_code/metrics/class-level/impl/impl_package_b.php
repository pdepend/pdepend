<?php
/**
 * @package b
 */
class A implements E, C {}

/**
 * @package b
 */
class C implements C {}

/**
 * @package b
 */
class B extends C implements D, A {}
?>