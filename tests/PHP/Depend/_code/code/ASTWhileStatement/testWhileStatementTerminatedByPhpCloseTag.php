<?php
function testWhileStatementTerminatedByPhpCloseTag($i = 0)
{
    while (++$i < 42):
        echo $i, PHP_EOL;
    endwhile
        ?>
    <?php
}