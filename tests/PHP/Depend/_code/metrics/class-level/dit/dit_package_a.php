<?php
/**
 * @package a
 */
interface I {}

/**
 * @package a
 */
class A implements I {}

/**
 * @package a
 */
class B extends A {}

/**
 * @package a
 */
class C extends B {}

/**
 * @package a
 */
class D extends C {}

/**
 * @package a
 */
class E extends C {}

/**
 * @package a
 */
class F extends E {}
?>