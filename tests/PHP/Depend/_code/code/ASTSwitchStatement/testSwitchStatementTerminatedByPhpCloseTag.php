<?php
function testSwitchStatementTerminatedByPhpCloseTag($i)
{
    switch ($i):
        case 42:
            break;
        default:
            break;
    endswitch
        ?>
    <?php
}