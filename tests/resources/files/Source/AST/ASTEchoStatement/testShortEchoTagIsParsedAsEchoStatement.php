<?php
function testShortEchoTagIsParsedAsEchoStatement()
{
    $x = '';
    ?>
        <?= $x ?>
    <?php
}
