<?php
/**
 * @package pkgB
 */

/**
 * @package pkgA
 */
class classB
{
    function methodBA() {}
    function methodBB() {}
}

/**
 * @package pkgA
 */
class classA
{
    function methodAB() {}
    function methodAA() {}
}

/**
 * @package pkgB
 */
interface interfsC
{
    function methodCB();
    function methodCA();
}

function funcD() {}
?>
