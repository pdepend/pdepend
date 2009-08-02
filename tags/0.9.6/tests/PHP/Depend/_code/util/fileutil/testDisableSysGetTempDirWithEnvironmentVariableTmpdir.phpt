--TEST--
Tests the PHP_Depend_Util_FileUtil::getSysTempDir() method with disabled
sys_get_temp_dir() method and the environment variable TMPDIR
--INI--
disable_functions=sys_get_temp_dir
--ENV--
TMPDIR=/pdepend_tmpdir
--FILE--
<?php
require_once 'PHP/Depend/Util/FileUtil.php';
var_dump(PHP_Depend_Util_FileUtil::getSysTempDir());
?>
--EXPECT--
string(15) "/pdepend_tmpdir"