<?php
function testForStatementTerminatedByPhpCloseTag($i = 0)
{
    for (; $i < 42; ++$i):
        echo $i, PHP_EOL;
    endfor
        ?>
    <?php
}