<?php
/**
 * File level comment.
 *
 * @package PHP
 * @subpackage Depend
 */

/**
 * Function without package should be PHP::Depend
 */
function afunc() {

}

/**
 * Function with package should be PHP_Depend::Test
 *
 * @package PHP_Depend
 * @subpackage Test
 */
function cfunc() {

}

// A function without package should be PHP::Depend
function bfunc() {

}