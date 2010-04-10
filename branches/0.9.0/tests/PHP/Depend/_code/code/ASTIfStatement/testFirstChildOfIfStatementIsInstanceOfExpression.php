<?php
function testFirstChildOfIfStatementIsInstanceOfExpression()
{
    if ($foo || $bar && $baz) {

    }
}