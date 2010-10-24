<?php
function testElseIfStatementGraphWithBooleanExpressions()
{
    if (true) {

    } elseif ($x && $y || $z) {
        
    }
}