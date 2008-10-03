<?php
/**
 * @package a
 */
interface A {}

/**
 * @package a
 */
interface B extends A {}

/**
 * @package a
 */
interface C extends A {}

/**
 * @package a
 */
interface F {}

/**
 * @package a
 */
interface E extends F {}

/**
 * @package a
 */
interface D extends B, E {}
?>