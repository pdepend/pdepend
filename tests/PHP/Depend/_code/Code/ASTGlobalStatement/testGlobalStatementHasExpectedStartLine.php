<?php
function testGlobalStatementHasExpectedStartLine()
{
    global $_SERVER,
           ${'SERVER'},
           $SERVER;
}