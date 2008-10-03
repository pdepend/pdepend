<?php
/**
 * @package pdepend.testing
 */

/**
 * @param array $a
 * @return Iterator
 * @throws MyException
 */
function foo(array $a)
{
    return new ArrayIterator($a);
}

/**
 * @package pdepend.testing
 */
class MyException extends Exception {}

/**
 * @package pdepend.testing2
 */
class YourException extends Exception {}