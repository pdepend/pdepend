--TEST--
Tests the PHP_Depend_Util_FileUtil::getSysTempDir() method with disabled
sys_get_temp_dir() method and the environment variable TMP
--INI--
disable_functions=sys_get_temp_dir
--ENV--
TMP=/pdepend_tmp
--FILE--
<?php
require_once 'PHP/Depend/Util/FileUtil.php';
var_dump(PHP_Depend_Util_FileUtil::getSysTempDir());
?>
--EXPECT--
string(12) "/pdepend_tmp"