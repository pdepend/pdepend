<?php
function testIfStatementTerminatedByPhpCloseTag($i = 0)
{
    if ($i < 42):
        echo $i, PHP_EOL;
    endif
        ?>
    <?php
}