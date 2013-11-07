<?php
/**
 * @package pdepend.test
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
 * @package pdepend.test
 */
class MyException extends Exception {}

/**
 * @package pdepend.testProjectAwareAnalyzerWithoutCode
 */
class YourException extends Exception {}