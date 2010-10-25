<?php
function testGlobalStatementHasExpectedStartColumn()
{
    global $_SERVER,
           ${'SERVER'},
           $SERVER;
}