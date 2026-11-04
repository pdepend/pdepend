<?php
function testShortEchoTagWithCommasIsParsedAsEchoStatement()
{
    $a = 'a';
    $b = 'b';
    ?>
        <p><?= $a, $b, '!' ?></p>
        <p><?= $a ?></p>
    <?php
    return $a;
}
