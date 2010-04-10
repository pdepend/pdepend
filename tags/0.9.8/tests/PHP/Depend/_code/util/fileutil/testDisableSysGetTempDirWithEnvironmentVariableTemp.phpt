--TEST--
Tests the PHP_Depend_Util_FileUtil::getSysTempDir() method with disabled
sys_get_temp_dir() method and the environment variable TEMP
--INI--
disable_functions=sys_get_temp_dir
--ENV--
TEMP=/pdepend_temp
--FILE--
<?php
require_once 'PHP/Depend/Util/FileUtil.php';
var_dump(PHP_Depend_Util_FileUtil::getSysTempDir());
?>
--EXPECT--
string(13) "/pdepend_temp"