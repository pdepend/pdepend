<?php
function testForeachStatementWithKeyAndValueByReference()
{
    foreach ($expr as $key => &$value) {}
}