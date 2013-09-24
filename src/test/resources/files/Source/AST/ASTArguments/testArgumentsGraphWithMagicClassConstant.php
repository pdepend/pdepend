<?php
function testArgumentsGraphWithMagicClassConstant($dispatcher, $count)
{
    $dispatcher->dispatch(__CLASS__, "run", array($count));
}
