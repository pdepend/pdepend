--TEST--
Tests the PHP_Depend_Util_MathUtil::add() method with the bcadd() function.
--SKIPIF--
<?php
if (!extension_loaded('bcmath')) {
    die('Skipped: bcmath extension required');
}
?>
--FILE--
<?php
require_once 'PHP/Depend/Util/MathUtil.php';
var_dump(PHP_Depend_Util_MathUtil::add(1000, 1000));
var_dump(PHP_Depend_Util_MathUtil::add(10000, 10000));
var_dump(PHP_Depend_Util_MathUtil::add(100000, 100000));
?>
--EXPECTREGEX--
string\(4\) ["\']2000["\']
string\(5\) ["\']20000["\']
string\(6\) ["\']200000["\']
