--TEST--
Tests the PHP_Depend_Util_FileUtil::getSysTempDir() method with disabled
sys_get_temp_dir() method and the mkdir() fallback
--ENV--
TMP=
TEMP=
TMPDIR=
--INI--
disable_functions=sys_get_temp_dir
--FILE--
<?php
require_once 'PHP/Depend/Util/FileUtil.php';

class TestFileStreamWrapper
{
    public function url_stat($path, $mode)
    {
        return false;
    }
    public function mkdir($path)
    {
        echo __METHOD__, '()', PHP_EOL, 'input: ';
        var_dump($path);
    }
}

stream_wrapper_unregister('file');
stream_wrapper_register('file', 'TestFileStreamWrapper');

$output = PHP_Depend_Util_FileUtil::getSysTempDir();
echo 'output: ';
var_dump($output);

stream_wrapper_restore('file');
?>
--EXPECT--
TestFileStreamWrapper::mkdir()
input: string(4) "/tmp"
output: string(4) "/tmp"