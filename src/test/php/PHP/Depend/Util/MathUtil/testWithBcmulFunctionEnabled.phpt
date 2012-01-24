--TEST--
Tests the PHP_Depend_Util_MathUtil::mul() method with the bcmul() function.
--SKIPIF--
<?php
if (!extension_loaded('bcmath')) {
    die('Skipped: bcmath extension required');
}
?>
--FILE--
<?php
require_once 'PHP/Depend/Util/MathUtil.php';
var_dump(PHP_Depend_Util_MathUtil::mul(1000, 1000));
var_dump(PHP_Depend_Util_MathUtil::mul(10000, 10000));
var_dump(PHP_Depend_Util_MathUtil::mul(100000, 100000));
?>
--EXPECTREGEX--
string\(7\) ["\']1000000["\']
string\(9\) ["\']100000000["\']
string\(11\) ["\']10000000000["\']
