<?php
function testForeachStatementTerminatedByPhpCloseTag($array)
{
    foreach ($array as $i):
        echo $i, PHP_EOL;
    endforeach
        ?>
    <?php
}