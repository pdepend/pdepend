--TEST--
Tests the \PDepend\Util\MathUtil::add() method with a disabled bcadd() function.
--INI--
disable_functions=bcadd
--FILE--
<?php
require_once 'PDepend/Util/MathUtil.php';
var_dump(\PDepend\Util\MathUtil::add(1000, 1000));
var_dump(\PDepend\Util\MathUtil::add(10000, 10000));
var_dump(\PDepend\Util\MathUtil::add(100000, 100000));
?>
--EXPECTREGEX--
string\(4\) ["\']2000["\']
string\(5\) ["\']20000["\']
string\(6\) ["\']200000["\']
