<?php
function testGlobalStatementHasExpectedEndColumn()
{
    global $_SERVER,
           ${'SERVER'},
           $SERVER;
}