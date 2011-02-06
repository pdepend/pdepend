<?php
function testForeachStatementThrowsExpectedExceptionForKeyByReference()
{
    foreach ($expr as &$key => &$value) {}
}