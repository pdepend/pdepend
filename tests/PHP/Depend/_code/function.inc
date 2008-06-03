<?php
/**
 * @package pdepend.test
 */

/**
 * @param array $a
 * @return unknown
 */
function foo(array $a)
{
    return new ArrayIterator($a);
}